var components = {
    "packages": [
        {
            "name": "moment",
            "main": "moment-built.js"
        },
        {
            "name": "moment-timezone",
            "main": "moment-timezone-built.js"
        }
    ],
    "baseUrl": "components"
};
if (typeof require !== "undefined" && require.config) {
    require.config(components);
} else {
    var require = components;
}
if (typeof exports !== "undefined" && typeof module !== "undefined") {
    module.exports = components;
}