// Use regex based on the search query from #equipment-select-search to only show relevant options in the multi-select form (#equipment-select)
// This is a workaround for the lack of a "search within" feature in the multi-select form

function search() {
    var input = document.getElementById("equipment-select-search");
    var filter = input.value.toUpperCase();
    var select = document.getElementById("equipment-select");
    var options = select.options;
    var optionsLength = options.length;
    for (var i = 0; i < optionsLength; i++) {
        var option = options[i];
        var optionText = option.text;
        var optionValue = option.value;
        if (optionText.toUpperCase().indexOf(filter) > -1) {
            option.style.display = "";
        } else {
            option.style.display = "none";
        }
    }
}