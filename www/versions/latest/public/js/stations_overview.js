
function cleanSearchString(str) {
	return str.replace(/[^a-zA-Z0-9\s!?]+/g, '').toLowerCase().trim();
}

/**
 * Takes an array of stations {StationID: , Station}.
 *
 * Provides access via getMatches to perform simple string matching queries.
 *
 */
function StationFilter(stations) {
	this.stations = stations;
}

StationFilter.prototype.filter = function(station, queries){
	var cmp = cleanSearchString(station);
	for(var i = 0; i < queries.length; i += 1) {
		if (cmp.search(queries[i]) === -1) {
			return false;
		}
	}
	return true;
};

/**
 * Finds best matches for searchText in stations.
 *
 * Returns list of matching stations.
 */
StationFilter.prototype.getMatches = function(searchText, limit) {
	searchText = cleanSearchString(searchText);
	if(limit === undefined) {
		limit = Number.MAX_VALUE;
	}

	var queries = searchText.split(" ");
	var stations = this.stations;

	var result = [];
	for(var i = 0; i < stations.length; i += 1) {
		var name = stations[i].Station;
		if(this.filter(name, queries)) {
			result[result.length] = stations[i];
			if(result.length > limit - 1) {
				break;
			}
		}
	}

	return result;
};

StationFilter.prototype.getAllStations = function() {
    return this.stations;
};
