/* Smiley set for editor, in natural order.
It must be in valid JSON format (double quotes, no syntax errors, etc.),
see http://www.php.net/manual/en/function.json-decode.php
Please try to use similar syntax in your sets, because the parser is quite sensitive.

name - smiley name (for internal use)
code - representation in text
file - representation as image
prio - priority for parsing. 1 is highest, 50 is normal. Usually smilies which may
intersect with other smilies have higher priorities over them.
lang - internationalized tip
*/
var smileSet = [
	{
		"name": "smile",
		"code": ":)",
		"file": "smile.gif",
		"prio": 60,
		"lang": smileL.smile
	},
	{
		"name": "happy",
		"code": ":))",
		"file": "happy.gif",
		"prio": 10,
		"lang": smileL.happy
	},
	{
		"name": "sad",
		"code": ":(",
		"file": "sad.gif",
		"prio": 60,
		"lang": smileL.sad
	},
	{
		"name": "wink",
		"code": ";)",
		"file": "wink.gif",
		"prio": 50,
		"lang": smileL.wink
	},
	{
		"name": "grin",
		"code": ":D",
		"file": "grin.gif",
		"prio": 50,
		"lang": smileL.grin
	},
	{
		"name": "surprised",
		"code": ":O",
		"file": "surprised.gif",
		"prio": 50,
		"lang": smileL.surprised
	},
	{
		"name": "tongue",
		"code": ":P",
		"file": "tongue.gif",
		"prio": 50,
		"lang": smileL.tongue
	},
	{
		"name": "confused",
		"code": ":/",
		"file": "confused.gif",
		"prio": 50,
		"lang": smileL.confused
	},
	{
		"name": "sunglasses",
		"code": "B)",
		"file": "sunglasses.gif",
		"prio": 50,
		"lang": smileL.sunglasses
	},
	{
		"name": "angry",
		"code": "X-(",
		"file": "angry.gif",
		"prio": 50,
		"lang": smileL.angry
	},
	{
		"name": "inlove",
		"code": ":-X",
		"file": "inlove.gif",
		"prio": 50,
		"lang": smileL.inlove
	},
	{
		"name": "sleeping",
		"code": "I-)",
		"file": "sleeping.gif",
		"prio": 50,
		"lang": smileL.sleeping
	},
	{
		"name": "rose",
		"code": "@};-",
		"file": "rose.gif",
		"prio": 10,
		"lang": smileL.rose
	},
	{
		"name": "angel",
		"code": "O:-)",
		"file": "angel.gif",
		"prio": 10,
		"lang": smileL.angel
	},
	{
		"name": "devil",
		"code": ">:)",
		"file": "devil.gif",
		"prio": 10,
		"lang": smileL.devil
	},
	{
		"name": "kiss",
		"code": ":-*",
		"file": "kiss.gif",
		"prio": 30,
		"lang": smileL.kiss
	}
];

// Editor dialog display properties
var smileBox = {
	"width": 200, // Width in px
	"height": 160, // Height in px
	"perRow": 4 // Smilies per row
};