"use strict";

/**
 * Browserify transform to inject jQuery alias globally
 * This transform adds 'const $ = jQuery;' at the top of each file
 * that doesn't already have a jQuery alias defined.
 */
var through = require('through2');

module.exports = function (file) {
  // Only process JavaScript files
  if (!file.endsWith('.js')) {
    return through();
  } // Skip node_modules


  if (file.includes('node_modules')) {
    return through();
  }

  return through(function (chunk, enc, cb) {
    var content = chunk.toString(); // Check if $ is already defined as jQuery alias

    var hasJQueryAlias = content.includes('const $ = jQuery') || content.includes('var $ = jQuery') || content.includes('let $ = jQuery') || content.includes('$ = jQuery'); // Check if it's already in an IIFE with $ parameter

    var hasIIFEWithJQuery = content.includes('( function( $ )') || content.includes('(function( $ )'); // If no jQuery alias is found, inject it

    if (!hasJQueryAlias && !hasIIFEWithJQuery) {
      var injectedContent = 'const $ = jQuery;\n' + content;
      this.push(Buffer.from(injectedContent));
    } else {
      this.push(chunk);
    }

    cb();
  });
};