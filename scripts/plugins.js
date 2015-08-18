(function(exports) {
    var names = ["Sunday", "Monday", "Tuesday", "Wednesday",
        "Thursday", "Friday", "Saturday"];

    exports.name = function(number) {
        return names[number];
    };
    exports.number = function(name) {
        return names.indexOf(name);
    };

    jQuery(document).ready(function () {
        jQuery('#farmer__animalSelect').chosen({
            search_contains: true
        });
    });

})(this.farmer__plugins = {});
