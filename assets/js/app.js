/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you require will output into a single css file (app.css in this case)
require('../css/app.css');

// Need jQuery? Install it with "yarn add jquery", then uncomment to require it.
// require jQuery normally
let $ = require('jquery');
global.$ = global.jQuery = $;
//console.log($);

let contactButton = $('#contact-button');
contactButton.click((e) => {
    e.preventDefault();
    $('#contact-form').slideDown();
    contactButton.slideUp();
});
