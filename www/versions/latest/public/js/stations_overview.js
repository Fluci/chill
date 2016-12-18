/*jshint
     esnext: true
 */
'use strict';

/**
 * Transform string to canonical form such that it can be compared.
 * @param  {string} str String to transform.
 * @return {string}     Reduced string.
 */
var cleanSearchString = function(str) {
    return str.replace(/[^a-zA-Z0-9\s!?]+/g, '').toLowerCase().trim();
};

/**
 * Takes an array of stations {StationID: , Station}.
 *
 * Provides access via getMatches to perform simple string matching queries.
 *
 * @param {Object[]} stationsList
 *
 * @constructor
 *
 */
var StationFilter = function(stationsList) {
    var stations = stationsList;

    /**
     * Checks if station and queries match.
     * @param  {Object} station
     *         Station for which needs to be checked if it matches.
     * @param  {string[]} queries list of strings that need to found in station.
     * @return {bool}         Decision: true if it matches, false otherwise.
     */
    var filter = function(station, queries) {
        var cmp = cleanSearchString(station);

        for (var i = 0; i < queries.length; i += 1) {
            if (cmp.search(queries[i]) === -1) {
                return false;
            }
        }
        return true;
    };


    /**
     * Finds best matches for searchText in stations.
     *
     * Returns list of at most `limit` matching stations.
     *
     * @param {string} searchText Search string that
     * each station needs to be matched with.
     *
     * @param {number} limit Maximum of matches requested.
     *
     * @return {Object[]} List of found stations.
     */
    this.getMatches = function(searchText, limit) {
        searchText = cleanSearchString(searchText);
        if (limit === undefined) {
            limit = Number.MAX_VALUE;
        }


        var queries = searchText.split(' ');
        var result = [];

        for (var i = 0; i < stations.length; i += 1) {
            var name = stations[i].Station;
            if (filter(name, queries)) {
                result[result.length] = stations[i];
                if (result.length > limit - 1) {
                    break;
                }
            }
        }

        return result;
    };

    /**
     * @return {Object[]} Complete list of all stations.
     */
    this.getAllStations = function() {
        return stations;
    };
};


/**
 * Creates a FilterTable object. It knows how to use a stationFilter and
 * where to place the content.
 *
 * @param {number} maxShowEntries Maximum number of search results to find.
 *
 * @constructor
 */
var FilterTable = function(maxShowEntries) {
    var lastQuery = null;
    // show first n results
    var maxShow = maxShowEntries;
    var tables = [];
    var stations = null;
    var resultInfo = null;
    var stopPointRef = null;

    this.setTables = function(tableElements) {
        tables = tableElements;
    };

    this.setStations = function(stats) {
        stations = stats;
    };

    this.setResultInfo = function(info) {
        resultInfo = info;
    };

    this.setStopPointRef = function(ref) {
        stopPointRef = ref;
    };
    /**
     * Creates HTML rows for given stations.
     * @param  {Object[]} insertStations List of stations to transform.
     * @return {string}                  The station formatted as HTML row.
     */
    var makeRows = function(insertStations) {
        var ins = [];
        for (var i = 0; i < insertStations.length; i += 1) {
            var e = insertStations[i];
            ins.push('<tr><td><a href="?stop_point_ref=');
            ins.push(e.StationID);
            ins.push('">');
            ins.push(e.Station);
            ins.push('</a></td></tr>');
        }
        return ins.join('');
    };

    /**
     * Searches stations for matches with searchText and
     * puts the results in the given tables.
     * @param  {string} searchText Query needs to be matched.
     * @return {void}  Nothing.
     */
    this.search = function(searchText) {
        searchText = cleanSearchString(searchText);

        if (stations === null) {
            resultInfo.innerText = 'Waiting for data ...';
            return;
        }

        if (searchText === lastQuery) {
            return;
        }

        lastQuery = searchText;

        var insert;
        if (searchText === '') {
            insert = [];
        } else {
            insert = stations.getMatches(searchText, maxShow + 1);
        }

        if (insert.length === 0) {
            for (var i = 0; i < tables.length; i += 1) {
                tables[i].innerHTML = '';
            }
            if (searchText === '') {
                resultInfo.innerText = '';
            } else {
                resultInfo.innerText = '0 rows';
            }
            stopPointRef.value = '-1';
            return 0;
        }

        var showSize = Math.min(maxShow, insert.length);
        var inserts = [];
        var lo = 0;

        for (var i = 0; i < tables.length; i += 1) {
            var hi = Math.ceil(showSize * (i + 1) / tables.length);
            var ins = insert.slice(lo, hi);
            var rows = makeRows(ins);
            tables[i].innerHTML = rows;
            lo = hi;
        }

        if (insert.length > 0) {
            stopPointRef.value = insert[0].StationID;
        }

        var moreRes = '';
        if (insert.length == maxShow + 1) {
            moreRes = ' shown';
        }

        resultInfo.innerText = showSize + ' results' + moreRes;
    };
};
