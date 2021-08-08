$(function() {
  //TODO: Introduce nextpanel to replace !system_id. 
	initAction()
});
function initAction() {
	$("#showMyOrg").click(showMyOrg);
	$("#showMyUser").click(showMyUser);
	$(".adminmenulink").click(function() {get_template($(this).data("page"),{},"whenLoaded_"+$(this).data("page"))}); 
}

function showMyOrg() {
	get_template("myOrganization",{contentdiv:"contentdiv"},"initOrg");
}
function showMyUser() {
	get_template("myUser",{},"editUserData");
}
function editUserData() {
	$(".userinput").change(function() {var val=($(this).hasClass("password")?md5($(this).val()):$(this).val()); send("usersettingsupdate","doNothing",{uservar:($(this).attr("name")?$(this).attr("name"):$(this).attr("id")),userval:val},"backend")});
	$("#finish").click(finish)
}

function initOrg() {
	$(".editorg").click(editorg)
	$("#resetgames").click(function() {send('resetgames',"doNothing",{},"backend")});
}
function initCSS() {
// 	return;
	hljs.configure({   // optionally configure hljs
		languages: ['css']
	});

	var quill = new Quill('#csseditor', {
	modules: {
			syntax: true,              // Include syntax module
			toolbar: false  // No toolbar
		},
		theme: 'snow'
	});
	var CSSchange = false
	quill.on('text-change', function(delta) {
		CSSchange = true
	});
	setInterval(function() {
		if (CSSchange) {
 			console.log('Saving changes', CSSchange);
			CSSeditsave()
			CSSchange = false
		}
	}, 5*1000);
	$("#editCSSsave").click(function() {CSSeditsave(true)})
	$("#refreshStyle").click(function() {send("saveCSS","refreshStyle",{CSS:$("#csseditor").find("pre").text()},"backend")})
}
function CSSeditsave(finish) {
	send("saveCSS",(finish?"showMyOrg":"pagesaved"),{CSS:$("#csseditor").find("pre").text()},"backend");
}
function refreshStyle() {
	$('#basesystemstyles').attr('href',$('#basesystemstyles').attr('href').replace(/\?.*/,"")+"?v="+ new Date().getTime());
}
function editorg() {
	switch($(this).attr("id")) {
		case "editOrgPage":
			get_template("editpage",{pagetype:"org",id:0,contentdiv:"orgcontentdiv"},"editpageready")
		break;
		case "editFrontPage":
			get_template("editpage",{pagetype:"frontpage",id:0,contentdiv:"orgcontentdiv"},"editpageready")
		break;
		case "editPages":
			get_template("orgpages",{contentdiv:"orgcontentdiv"},"orgpagesready")
		break;
		case "editUserPage":
			get_template("editpage",{pagetype:"user",id:0,contentdiv:"orgcontentdiv"},"editpageready")
		break;
		case "editHeaderPage":
			get_template("editpage",{pagetype:"header",id:0,contentdiv:"orgcontentdiv"},"editpageready")
		break;
		case "editStyles":
			$("#orgcontentdiv").html('<h2 class="text-center align-middle h-100">'+_('Preparing CSS-editor')+'</h2><div class="d-flex justify-content-center"><div class="spinner-border m-5" role="status"><span class="sr-only">Loading...</span></div></div>');
			get_template("editCSS",{contentdiv:"orgcontentdiv"},"initCSS");
		break;
		case "editOrgSettings":
			get_template("orgsettings",{contentdiv:"orgcontentdiv"},"setsettings")
		break;
		case "editMasterData":
			get_template("orgmasterdata",{contentdiv:"orgcontentdiv"},"editMasterData")
		break
		case "editOrgUsers":
			editOrgUsersIni()
		break
		case "editOrgStats":
			console.log("stats");
			get_template("stats",{contentdiv:"orgcontentdiv"},"doNothing")
		break
	}
}
function editOrgUsersIni() {
	get_template("orgusers",{contentdiv:"orgcontentdiv"},"editOrgUsers")	
}
function editOrgUsers() {
 	$(".changePermissions").click(function() {if($(this).children().length==0) send("systemUserPermissions","selectUserPermissions",{user:$(this).data("user")},"backend")})
	$("#invite").click(function() {send("orgUser","editOrgUsersIni",{invite:$("#email").val(),inviteuser:$("username").val()},"backend")})
	$(".remove").click(function() {send("orgUser","editOrgUsersIni",{remove:$(this).data("user")},"backend")})
	$("#create").click(function() {send("orgUser","editOrgUsersIni",{create:$("#email").val(),user:$("username").val(),password:md5($("#password").val())},"backend")})
	
}
function selectUserPermissions(json) {
	var cp=$(".changePermissions[data-user="+json.user+"]")
	$(cp).html(json.select)
	$("#doChangePermissions").change(doChangePermissions)
}
function doChangePermissions() {
	var val=$(this).val()
	var user=$(this).parent().data("user")
	$(this).parent().html(val)
	send("setSystemUserPermissions","doNothing",{permissions:val,user:user},"backend")
}

function editMasterData() {
	$(".orginput").change(function() {send("orgsettingsupdate","doNothing",{orgvar:$(this).attr("id"),orgval:$(this).val()},"backend")});
	$("#finish").click(finish)
}
function orgpagesready() {
	$("#newpage").click(newpage)
	$(".pagetitleedit").click(pagetitleedit)
	$(".orgpageedit").click(function() {get_template("editpage",{pagetype:"org",pagetitle:$(this).parent().siblings().first().text(),id:0,contentdiv:"orgcontentdiv"},"editpageready")})
}
function newpage() {
	var pagetitle=window.prompt("Page title")
	if(typeof(pagetitle)!="undefined" && pagetitle!="") get_template("editpage",{pagetype:"org",pagetitle:pagetitle,newpage:true,id:0,contentdiv:"orgcontentdiv"},"editpageready")
}
function pagetitleedit() {
	var oldtitle=$(this).text()
	console.log(oldtitle)
	var pagetitle=window.prompt("New Page title",oldtitle.trim())
	
	if(pagetitle!=null && pagetitle!="") send("changepagetitle","editPages",{pagetitle:pagetitle.trim(),oldtitle:oldtitle},"backend")
}
function editPages(json) {
	console.log(json)
	get_template("orgpages",{contentdiv:"orgcontentdiv"},"orgpagesready")
}
jQuery.cachedScript = function( url, options ) {
 
  // Allow user to set any option except for dataType, cache, and url
  options = $.extend( options || {}, {
    dataType: "script",
    cache: true,
    url: url
  });
 
  // Use $.ajax() since it is more flexible than $.getScript
  // Return the jqXHR object so we can chain callbacks
  return jQuery.ajax( options );
};
 
// Usage
// $.cachedScript( "ajax/test.js" ).done(function( script, textStatus ) {
//   console.log( textStatus );
// });
function editpageready(json) {
	if($("#quillscss").length==0)
		 $('head').append('<link href="//cdn.quilljs.com/1.3.6/quill.snow.css" id="quillcss" rel="stylesheet" type="text/css">')
		 
	$.cachedScript("//cdn.quilljs.com/1.3.6/quill.min.js").done(function() {
		$.cachedScript("./js/vendor/image-resize.min.js").done(function() {
			$.cachedScript("./js/vendor/image-drop.min.js").done(function() {
				var toolbarOptions = [
				[{
				'header': [1, 2, 3, 4, 5, 6, false]
				}],
				['bold', 'italic', 'underline', 'strike'], // toggled buttons
				['blockquote', 'code-block'],

				[{
				'list': 'ordered'
				}, {
				'list': 'bullet'
				}],
				[{
				'indent': '-1'
				}, {
				'indent': '+1'
				}], // outdent/indent
				[{
				'size': ['small', false, 'large', 'huge']
				}], // custom dropdown
				[{
				'color': []
				}, {
				'background': []
				}], // dropdown with defaults from theme
				[{
				'font': []
				}],
				[{
				'align': []
				}],
				['link', 'image', 'video'],

				['clean'] // remove formatting button
			];

				var quill = new Quill('#editor', {
					modules: {
						toolbar: toolbarOptions,
						imageResize: {
							modules: [ 'Resize', 'DisplaySize', 'Toolbar' ]
						},
						imageDrop: true
			// 			autoformat: true
					},
					theme: 'snow',
				});
				var change = false
				quill.on('text-change', function(delta) {
					change = true
				});
				
				if(json.headers) {
					headers=json.headers // headers include: headers[1][headernum]: classes, headers[2][headernum]: headercontent, 
					$("#headernum").change(function() {setheader(quill,$(this).children(":selected").val()) })
					$("#deleteheader").click(function() {deleteheader(quill)})
					$("#addheader").click(function() {addheader(quill)})
					$("#defaultvisibility ,#showfrom, #hidefrom").change(function() {updateheaders(json,quill)})
					setheader(quill,0)
				}

				// Save periodically
				setInterval(function() {
					if (change) {
			// 			console.log('Saving changes', change);
						pageeditsave(json,quill)
						change = false
					}
				}, 5*1000);

				// Check for unsaved data
				window.onbeforeunload = function() {
					if (change) {
						return _('There are unsaved changes. Are you sure you want to leave?');
					}
				}	
				$("#pageeditsave").click(function() {pageeditsave(json,quill,true)})
				// 	$('#editor').wysiwyg();
			})
		})
	})
}
function pageeditsave(json,quill,finish) {
	var pagecontent=quill.root.innerHTML
// 	console.log(json.pagetype)
	if(json.pagetype=="header") {
		var activeheader=$("#headernum").children(":selected").val()
		headers[2][activeheader]=pagecontent
		pagecontent=""
		for(i in headers[1]) {
			pagecontent+='<div class="'+headers[1][i]+'">'+headers[2][i]+'</div>'
		}
// 		console.log(pagecontent)
	}
	send("savepagecont",(finish?"finish":"pagesaved"),{id:json.id,pagetype:json.pagetype,pagecontent:pagecontent},"backend");
}

function addheader(quill) {
	var newnum=headers.length-1
	headers[1][newnum]=""
	headers[2][newnum]=""
	
	$("#headernum").append("<option>"+newnum+"</option");
	setheader(quill,newnum)
}
function deleteheader(quill) {
	var activeheader=$("#headernum option:selected").val()
	delete(headers[1][activeheader])
	delete(headers[2][activeheader])
	$("#headernum option:selected").remove()
	setheader(quill,$("#headernum option:first").val())
}
function setheader(quill,activeheader) {
	var c=headers[1][activeheader].split(" ")
	$("#headernum").val(activeheader)
	$("#defaultvisibility").val(c[0])
	$("#showfrom").val(/-block/.test(c[1])?c[1]:(/-block/.test(c[2]))?c[2]:"")
	$("#hidefrom").val(/-none/.test(c[1])?c[1]:(/-none/.test(c[2]))?c[2]:"")
	quill.root.innerHTML=headers[2][activeheader]
}
function pagesaved() { //Do nothing
}
function finish(json) {
	var pagetype=(json.pagetype?json.pagetype:$(this).data("pagetype")) 
	switch(pagetype) {
		case "org":
		case "frontpage":
		case "header":
			showMyOrg()
		break
 		case "user":
 			showMyUser()
 		break
		case "startobserve":
			get_template("startobserve",{},"whenLoaded_startobserve");
 		break
	}
}
