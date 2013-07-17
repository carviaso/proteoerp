/**
 * CSS Inline Transform v0.1
 * http://tikku.com/css-inline-transformer
 *
 * Copyright 2010, Nirvana Tikku
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://docs.jquery.com/License
 *
 * This tool leverages the jQuery library.
 *
 * Compatibility only tested with FireFox 3.5+, for now.
 *
 * @author Nirvana Tikku
 * @dependent jQuery 1.4
 * @date Wed Mar 31 14:58:04 2010 -0500
 *
 */

function doInline(){
	inlinify();
}

/**
 * @param css - the css that will be inserted into the stylesheet
 *				that is appended to the 'head' tag
 */
function createAndAppendStylesheet(css){
	var $style = jQuery("<style type='text/css' id='_delcss'></style>");
	$style.append(css);
	jQuery("head").append($style);
};


/**
 * @param stylesArray - the array of string
 * 			"{name}:{value};" pairs that are to be broken down
 *
 */
function createCSSRuleObject(stylesArray){
	var cssObj = {};
	for(_s in stylesArray){
		var S = stylesArray[_s].split(":");
		if(S[0].trim()==""||S[1].trim()=="")continue;
		cssObj[S[0].trim()] = S[1].trim();
	}
	return cssObj;
}

/**
 * @param $out - the tmp html content
 *
 */
function interpritAppendedStylesheet($out){
	var len = jQuery("head").find("[type=\"text/css\"]").length;
	var stylesheet = document.styleSheets[len-1];
	for(r in stylesheet.cssRules){
		try{
			var rule = stylesheet.cssRules[r];
			if(!isNaN(rule))break; // make sure the rule exists
			var $destObj = $out.find(rule.selectorText);
			var obj = rule.cssText.replace(rule.selectorText, '');
			obj = obj.replace('{','').replace('}',''); // clean up the { and }'s
			var styles = obj.split(";"); // separate each
			$destObj.css(createCSSRuleObject(styles)); // do the inline styling
		} catch (e) { }
	}

};

/**
 * The main method - inflinify
 *	this utilizes two text areas and a div for final output -
 * 		(1) css input textarea for the css to apply
 * 		(2) html content for the css to apply TO
 * 		(3) the final output div where the inlinified code is presented
 */
function inlinify(){
	var cssContent = jQuery("#css_input").val();
	createAndAppendStylesheet(cssContent);
	var htmlContent = jQuery("#html_input").val();
	var $tmpOutput = jQuery("<span></span>").html(htmlContent);
	interpritAppendedStylesheet($tmpOutput);
	jQuery("#final").val($tmpOutput.html());
};
