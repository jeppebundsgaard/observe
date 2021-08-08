var rowIcons='<span class="m-2" style="position:absolute"><i class="fas fa-trash deleteicon deleteRow"  title="'+_('Delete row')+'"></i>&nbsp;&nbsp;<i class="fas settingsIcon fa-palette changeColor " title="'+_('Change Row Color')+'"></i></span>'
var row='<div class="row obsrow rowSortable">'+rowIcons+'<div class="card-header rowHeader editableText" data-emptytxt="'+_('Row Name')+'"></div></div>'
var column='<div class="col p-0 obscol columnSortable"><div><span><i class="fas settingsIcon fa-palette changeColor float-right m-2" style="position:relative" title="'+_('Change Column Color')+'"></i></span><div class="card-header colHeader editableText" data-emptytxt="'+_('Column Name')+'"></div></div></div>'
var valuelist='<ul class="list-group list-group-flush categorySortable"></ul>'
var category='<div class="card obscategory"><div class="card-header categoryHeader editableText" data-emptytxt="'+_('Category Name')+'"></div></div>'
var value='<li class="list-group-item obsvalue"><span class="valueText editableText" data-emptytxt="'+_('Value')+'"></span></li>'
var textarea='<textarea class="textareaText" placeholder="'+_("Text in this box will be shown as placeholder text to the observer")+'"></textarea>'

var colFooter=' <div class="col float-right colFooter"><span title="'+_('Delete column')+'"><i class="fas fa-trash deleteicon float-right deleteCol"></i></span></div>'
var categoryFooter='<div class="card-footer categoryFooter"><div class="row"><div class="col settingsIcon"> <i class="fas fa-hand-holding changeMandatory" title="'+_("Not mandatory")+'" data-alttitle="'+_("Is mandatory")+'"></i> <i class="fas fa-check changeMultiple collapse show"  title="'+_("Only one choice possible")+'" data-alttitle="'+_("Multiple choices possible")+'"></i></div><div class="col p-0 text-center settingsIcon"> <i class="fas fa-key makeDependant"></i> <i class="fas fa-palette changeColor"></i> <i class="fas fa-list-ul changeType" title="'+_("Type: List. Click to change to text")+'" data-alttitle="'+_("Type: Text. Click to change to list")+'"></i></div><div class="col"><span title="'+_('Delete category')+'"> <i class="fas fa-trash deleteicon float-right deleteCat"></i></div></div></div>'

var newRowButton='<div class="row  newRow" title="'+_("Insert new row")+'"><div class="col newicon"><i class="fas fa-plus-circle"></i></div></div>'
var newColumnButton='<div class="col-1 newColumn newicon" title="'+_("Insert new column")+'"><i class="fas fa-plus-circle"></i></div>'
var newCategoryButton='<div class="card newCategory w-25 mx-auto" title="'+_("Insert new category")+'"><div class="card-header newicon"><i class="fas fa-plus-circle"></i></div></div>'
var newValueButton='<li class="list-group-item newValue  newicon" title="'+_("Insert new value")+'"><i class="fas fa-plus-circle"></i></li>'

var deleteValMarkup='<i class="fas fa-trash deleteicon float-right deleteVal" title="'+_('Delete value')+'">'

var colorClasses=["bg-primary","bg-secondary","bg-success","bg-danger","bg-warning","bg-info","bg-dark","bg-light"]
var textClasses=["text-white","text-white","text-white","text-white","text-white","text-white","text-white","text-dark"]

var objmarkup='<ul class="list-group"></ul>'
var objvaluemarkup='<li class="list-group-item objvalue"></li>'

var dependantCategory
var autosaveHandler
var obsid=0
var study_id
var institution_id

// Events to look out for...
var insertingTextActive=false
var dontEditValueText=false
var institutions
var institutioncontext

var sessions
var context
var numobsshow
var bracketcategories // The categories to name and give coler after when observations are shown as curley brackets

$(function() {
	whenLoaded_startobserve()
})
$.fn.extend({
	classOverlap: function(c) {
		var overlap=this.attr("class").split(" ").filter(value => -1 !== c.indexOf(value))
		return overlap.length==1?overlap[0]:""
	}
})
///////////////////////////////////////////////
// 
// View observations
function whenLoaded_viewobservations(json) {
	sessions=json.sessions
	context=json.context
	populateSessionTable()
	$("#viewgraphics").click(onoffbutton)
	$("#viewgraphics").html($("#viewgraphics").data("off"))
	$("#next").click(function() {populateObservationTable(1)})
	$("#prev").click(function() {populateObservationTable(-1)})
 	$("#savesettings").click(saveviewsettings)
	$("#categoryOrder").sortable().disableSelection();

}
function saveviewsettings() {
	send("saveviewsettings","viewsettingssaved",{
		study_id:context.study_id,
		categoryOrder:$("#categoryOrder").children().map(function(x) {return $(this).text()}).get(),
		curleyCategories:$("#curleyCategories").val()
	},"backend");
	$("#settingsmodal").modal("hide")
}
function viewsettingssaved(json) {
	context=json.context
	$("#obstablehead>th:nth-child(n+3)").each(function(i) {$(this).html($("#categoryOrder>li:nth-child("+(i+1)+")").text())});
	populateObservationTable(0)
//	populateSessionTable()
}
function onoffbutton() {
	var on=($(this).text()==$(this).data("on"))
	$(this).html(on?$(this).data("off"):$(this).data("on"))
	on=!on
	drawAllBrackets(on)
}
function populateSessionTable() {
	var stable=$("#sessions")
	stable.html("")
	var i=0
	for(var s of sessions) {
		var row="<tr>"
		var inst=[]
		for(var c of ["round","date","starttime","endtime","institution_id","groups","participants","subject","observer"]) {
			var val=""
			switch(c) {
				case "institution_id":
					inst=context.institutions.filter(function(x) {return x.id==s[c]})
					if(inst.length>0)
						val=inst[0]["name"]
					break
				case "groups":
				case "participants":
					//We do have the instution from the previous cell
					if(inst.length>0) {
						var obj=inst[0][c]
						var valarr=[]
						for(o of s[c]) {
							var e=obj.filter(function(x) {return x.id==o})
							if(e.length>0)
								valarr[valarr.length]=e[0]["name"]
						}
						val=valarr.join(", ")
					}
					break
				case "starttime":
				case "endtime":
					val=s[c].replace(/:00$/,"")
					break
				default:
					val=s[c]
			}
			row+='<td>'+val+'</td>'
		}
		row+="</tr>"
		var r=$(row)
		stable.append(r)
		r.data("num",i)
		r.click(openObservation)
		i++
	}
}
function openObservation(e) {
	// We need to do it this way to get a size of the modal col to hand over to canvas
	$("#obsmodal").on("shown.bs.modal",function() {populateObservationTable(e)})
	$("#obsmodal").modal("show")
}
function populateObservationTable(e) {
	$("#obsmodal").unbind("shown.bs.modal")
	if(typeof(e.type)!="undefined") {
		numobsshow=Number($(e.delegateTarget).data("num"))
	} else numobsshow+=e
	if(numobsshow<0) {
		numobsshow=0; 
		showWarning(_("This was the first observation"))
		return
	}
	if(numobsshow>=sessions.length) {
		numobsshow=sessions.length-1
		showWarning(_("This was the last observation"))
		return
	}

	var otable=$("#observations")
	otable.html("")
	$("#observer").html($("#sessions>tr:nth-child("+(numobsshow+1)+")>td:nth-child(9)").text())
	$("#inst").html($("#sessions>tr:nth-child("+(numobsshow+1)+")>td:nth-child(5)").text())
	$("#date").html($("#sessions>tr:nth-child("+(numobsshow+1)+")>td:nth-child(2)").text())
	$("#groups").html($("#sessions>tr:nth-child("+(numobsshow+1)+")>td:nth-child(6)").text())
	$("#participants").html($("#sessions>tr:nth-child("+(numobsshow+1)+")>td:nth-child(7)").text())
	for(var o of sessions[numobsshow].observations) {
		var row="<tr>"
		var cols=["starttime","endtime"].concat(context.obsscheme.categories)
		for(var c of cols) {
			var val=(typeof(o[c])=="undefined"?"":o[c])
			row+='<td>'+val+'</td>'
		}
		row+="</tr>"
		var r=$(row)
		otable.append(r)
	}
	var canvasWidth=$("#obscanvas").parent().innerWidth()
	$("#obscanvas").attr("width",canvasWidth)
	drawBrackets(numobsshow,$("#obscanvas"),canvasWidth)
}
function drawAllBrackets(on) {
	if(!on) {
		$(".curleygraph").remove()
	} else {
		var canvasWidth=$("#sessions").parent().innerWidth()
		var ntd=$("#sessions>tr:first>td").length
		$("#sessions>tr").each(function() {
			var numobs=$(this).data("num")
			var tr=$('<tr class="curleygraph"><td colspan="'+ntd+'"></td></tr>')
			var canvas=$('<canvas width="'+canvasWidth+'"></canvas>')
			tr.children().append(canvas)
			$(this).after(tr)
			drawBrackets(numobs,canvas,canvasWidth)
		})
	}
}
function drawBrackets(numobs,canvas,canvasWidth) {
	var canvasDOM=canvas.get(0)
	var startx=10
	var totalCurleyWidth=canvasWidth-2*startx
	var starty=60
	var height=25
	var totalTime=timeToMin(sessions[numobs].endtime)-timeToMin(sessions[numobs].starttime)
	bracketcategories=(typeof(context.settings.curleyCategories)=="object"?context.settings.curleyCategories:[context.obsscheme.categories[0]])
	var i=0
	for(var o of sessions[numobs].observations) {
		var minutes=timeToMin(o.endtime)-timeToMin(o.starttime)
		var timepercent=(minutes)/totalTime
		var pointtext=bracketcategories.map(x=>o[x]).join(", ")
		drawCurlyBracket(canvasDOM,startx,starty,totalCurleyWidth*timepercent,height,pointtext.hashCode(),"#000",pointtext,o.starttime,String(minutes)+_(" m."),((i==sessions[numobs].observations.length-1)?o.endtime:""))
		startx+=totalCurleyWidth*timepercent
		i++
	}
}
function timeToMin(t) {
	var tarr=t.split(":")
	return Number(tarr[0])*60+Number(tarr[1])
}
function drawCurlyBracket(canvas,startx,starty,width,height,color,textcolor,pointtext="",starttext="",middletext="",endtext="") {
    const margin=10;
    const fontFamily='Verdana, sans-serif';
	const fontSize='11pt'
	const textWidth=5
	const shadowColor = 'gray';
	const shadowBlur = 3;
	const shadowOffsetX = 2;
	const shadowOffsetY = 2;
	const lineWidth= 3
	const strokeWidth = 1//Fontstroke
	const lineCap="round"
    
	// Define the points as {x, y}
	const start = { x: startx,    y: starty  };

	const cp1={x: start.x, y: start.y} // Control point
	const cp2={x: start.x, y: start.y+height/3} // Control point

	const point2={x: start.x+width/16, y: start.y+height/3} // Start/end point

	const cp3={x:start.x+width/16, y: start.y+height/3} // Control point
	const cp4={x:start.x+width/2, y: start.y+height/5} // Control point

	//Middle point
	const point3={x: start.x+width/2, y: start.y+height} // Start/end point

	const cp5={x: start.x+width/2, y: start.y+height/5} // Control point
	const cp6={x: start.x+width-width/16, y: start.y+height/3} // Control point

	const point4={x: start.x+width-width/16, y: start.y+height/3} // Start/end point

	const cp7={x: start.x+width, y: start.y+height/3} // Control point
	const cp8={x: start.x+width, y: start.y} // Control point

	const point5={x: start.x+width, y: start.y} // End point

	// Define context
	const ctx = canvas.getContext('2d');

	// Design of bracket and text
	ctx.strokeStyle= color
	ctx.shadowColor = shadowColor;
	ctx.shadowBlur = shadowBlur;
	ctx.shadowOffsetX = shadowOffsetX;
	ctx.shadowOffsetY = shadowOffsetY;
	ctx.lineWidth = lineWidth
	ctx.lineCap = lineCap
	ctx.font = fontSize+" "+ fontFamily,

	ctx.beginPath();
	ctx.moveTo(start.x, start.y);

	ctx.bezierCurveTo(cp1.x, cp1.y, cp2.x, cp2.y, point2.x, point2.y);
	ctx.stroke();
	ctx.bezierCurveTo(cp3.x, cp3.y, cp4.x, cp4.y, point3.x, point3.y);
	ctx.stroke();
	ctx.bezierCurveTo(cp5.x, cp5.y, cp6.x, cp6.y, point4.x, point4.y);
	ctx.stroke();
	ctx.bezierCurveTo(cp7.x, cp7.y, cp8.x, cp8.y, point5.x, point5.y);
	ctx.stroke();

	drawText(ctx,startx+width/2, starty+height+margin, pointtext, color, -20, 'right',width)
	drawText(ctx,startx-textWidth, starty-margin, starttext, textcolor, 90, 'right',width)
	drawText(ctx,startx+width/2, starty-margin, middletext, textcolor, 0, 'center',width)
	drawText(ctx,startx+width-textWidth, starty-margin, endtext, textcolor, 90, 'right',width)
}
function drawText(ctx,x,y,text,color,rotate,align='right',maxwidth=0) {
	if(text!="") {
		ctx.save();
		ctx.fillStyle=color
		ctx.shadowColor = "transparent";
		ctx.translate(x, y);
		ctx.rotate(Math.PI / 180 * rotate);
		ctx.textAlign = align;
		ctx.fillText(text, 0, 0, maxwidth);
		ctx.restore();
	}
}
String.prototype.hashCode = function() {
  var hash = 0, i, chr, len;
  if (this.length == 0) return hash;
  for (i = 0, len = this.length; i < len; i++) {
    chr   = this.charCodeAt(i);
    hash  = ((hash << 5) - hash) + chr;
    hash |= 0; // Convert to 32bit integer
  }
  return "#"+hash.toString(16).slice(-6);
};


///////////////////////////////////////////////
//
// Observe
function whenLoaded_startobserve(json) {
	$(".observestudy").click(function() {send("studychosen","studychosen",{study_id:$(this).data("studyid")},"backend")})
	$("#startobserve").click(startobserve)
	$(".firstvisitlink").click(function() {get_template($(this).data("page"),{},"whenLoaded_"+$(this).data("page"))}); 

}
function studychosen(json) {
	$("#chooseinstitution").collapse("show")
	study_id=json.study_id
	$(".study:not(.study"+study_id+")").removeClass("d-flex").collapse("hide")
	$("#startobserve").data("obsid",json.observation_scheme)
	if(json.institutions.length==0 && json.subjects.length==0) {
		$("#startobserve").trigger("click")
	}
	else {
		if(json.institutions.length!=0){
			var ul=$(objmarkup)
			for(inst of json.institutions) {
				var instobj=$(objvaluemarkup)
				instobj.addClass("inst inst"+inst.id)
				instobj.data("instid",inst.id)
				instobj.html(inst.name)
				ul.append(instobj)
			}
			$("#institutioncol").collapse("show")
			$("#institutions").append(ul)
			$(".inst").click(function(e) {send("institutionchosen","institutionchosen",{study_id:json.study_id,instnum:$(this).index()},"backend"); toggleOne(e)})
		}
		if(json.subjects.length!=0){
			ul=$(objmarkup)
			for(subject of json.subjects) {
				var subjectobj=$(objvaluemarkup)
				subjectobj.addClass("subjectval")
				subjectobj.html(subject)
				ul.append(subjectobj)
			}
			$("#subjectcol").collapse("show")
			$("#subjects").append(ul)
			if(json.institutions.length==0)
				$(".subjectval").click(function() {$("#startobserve").trigger("click")})
			else
				$(".subjectval").click(toggleOne)
		}
	}
}
function toggleOne(e) {
	$(e.delegateTarget).toggleClass("valueSelected",500, "linear")
	$(e.delegateTarget).siblings().toggleClass("valueSelected",false,500, "linear")
}
function institutionchosen(json) {
	$("#choosegs").collapse("show")
	institution_id=json.inst_id
// 	$(".objvalue:not(inst"+institution_id+")").collapse("hide")
	if(json.groups==null && json.participants==null) {
		$("#startobserve").trigger("click")
	} else {
		if(json.groups!=null) {
			var ul=$(objmarkup)
			for(group of json.groups) {
				var groupobj=$(objvaluemarkup)
				groupobj.addClass("groupval")
				groupobj.data("id",group.id)
				groupobj.html(group.name)
				ul.append(groupobj)
			}
			$("#groups").append(ul)
			$("#groupcol").collapse("show")
		}
		if(json.participants!=null) {
			ul=$(objmarkup)
			for(participant of json.participants) {
				var participantobj=$(objvaluemarkup)
				participantobj.addClass("participantval")
				participantobj.data("id",participant.id)
				participantobj.html(participant.name)
				ul.append(participantobj)
			}
			$("#participantcol").collapse("show")
			$("#participants").append(ul)
		}
		$(".participantval,.groupval").click(function() {$(this).toggleClass("valueSelected",500, "linear")})
	}
}
function startobserve() {
	institutioncontext={study_id:study_id,institution_id:institution_id,groups:getgs("groups"),participants:getgs("participants"),subject:$(".subjectval.valueSelected").text()}
	localStorage.setItem("institutioncontext_"+$(this).data("obsid"),JSON.stringify(institutioncontext))
	get_template("observe",{id:$(this).data("obsid"),study_id:study_id,previousPage:"startobserve"},"whenLoaded_observe")
}
function getgs(o) {
	return $("#"+o+" .valueSelected").map(function(){return $(this).data("id")}).get()
}

////////////////////////////////////////////////////////
//
// Study administration 

function whenLoaded_studies(json) {
	$("#newStudy").click(function() {get_template("edit_study",{id:0},"whenLoaded_edit_study")})
	$(".editstudy").click(function() {get_template("edit_study",{id:$(this).parent().data("id")},"whenLoaded_edit_study")})
	$(".changestatus").click(function(e) {changestatus(e,json.statuses)})
	$(".viewobservations").click(function() {get_template("viewobservations",{study_id:$(this).parent().data("id")},"whenLoaded_viewobservations")})
	$(".export").click(function() {window.open("./backend/exportdata.php?study_id="+$(this).parent().data("id"))})
	$(".deletestudy").click(function() {if(window.confirm(_('Do you really want to delete this study? It will also delete all participants and institutions created for the study. It CANNOT be undone.'))) send("deletestudy","studydone",{id:$(this).parent().data("id")},"backend")})
}
function changestatus(e,statuses) {
	var thissstatus=$(e.delegateTarget).data("status")
	$(e.delegateTarget).unbind("click")
	var study_id=$(e.delegateTarget).parent().data("id")
	var statusoptions=""
	for(status in statuses) {
		statusoptions+='<option value="'+status+'" '+(status==thissstatus?'selected="selected"':'')+'>'+statuses[status]+'</option>'
	}
	var statusselect=$('<select id="status" class="font-control">'+statusoptions+'</select>')
	$(e.delegateTarget).html(statusselect)
	
	statusselect.change(function() {send("editval","studydone",{study_id:study_id,editval:"status",value:$(this).val()},"backend")}) 
}
function studydone() {
	get_template("studies",{},"whenLoaded_studies")
}
function whenLoaded_edit_study(json) {
	institutions=json.institutions
	$("#done").click(function() {get_template("studies",{},"whenLoaded_studies")})
	$(".addtopool").click(addtopool)
	$("#newround").change(addnewround)
	$("#activeround").change(changeround)
	$(".editval").change(editval)
	$("#selectobsscheme").click(selectobsscheme)
	$(".editinstitution").click(editinstitution)
	$(".subjectspan").click(editsubject)
	
	$(".deleteinst").click(deleteinst)
	$(".deleteobserver").click(deleteobserver)
	$(".deletesubject").click(deletesubject)
	$("#saveinstitution").click(saveinstitution)
	$(".addobj").click(addtopool)
	$(".addobjs").focus(function() {$(this).addClass("activeaddobjs")})
	if($("#study_id").val()==0) {
		$(".notname").attr("disabled",true)
	}
}
function selectobsscheme(privtoggle) {
	send("selectobsscheme","obsschemeselected",{study_id:$("#study_id").val(),publicschemes:$("#usepublic:checked").length>0},"backend")
}
function obsschemeselected(json) {
	$("#privpublbutton").html(json.button)
	$('.bs-toggle').bootstrapToggle()
	$('.bs-toggle').change(function() {	selectobsscheme(true)})

	$("#obsschemes").html(json.html)
	$("#obsschememodal").modal("show")
	$(".chooseobsscheme").click(chooseobsscheme)
}
function chooseobsscheme() {
	send("chooseobsscheme","donothing",{study_id:$("#study_id").val(),obsid:$(this).data("obsid"),transid:$(this).data("transid")},"backend")
	$(".flagthumb").attr("src","img/flags/"+$(this).data("language")+".png").attr("alt",$(this).attr("alt"))
	$("#obsschemename").html($(this).closest(".obscard").find(".card-title").text())
	$("#obsschememodal").modal("hide")
}
function addnewround() {
	var newround=$(this).val()
	$("#activeround").append('<option value="'+newround+'">'+newround+'</option>')
	$("#activeround").val(newround)
	$(this).val("")
	send("updatejson","donothing",{study_id:$("#study_id").val(),arrayappend:true,col:"rounds",whereandvals:["$.rounds",newround]},"backend")
	$("#activeround").trigger("change")
}
function changeround() {
	send("updatejson","donothing",{study_id:$("#study_id").val(),update:true,col:"rounds",whereandvals:["$.active",$(this).val()]},"backend")

}
function editval() {
	send("editval","afteredit",{study_id:$("#study_id").val(),editval:$(this).attr("id"),value:$(this).val()},"backend")
}
function afteredit(json) {
	if(typeof(json.study_id)!="undefined") {
		$("#study_id").val(json.study_id)
		$(".notname").attr("disabled",false)

	}
}
function editinstitution() {
	var instnum=$(this).parent().data("instnum")
	var inst=institutions[instnum]
	var m=$("#instmodal")
	$("#instnum").val(instnum)
	m.find('#instid').val(inst.id)
	m.find('#instname').val(inst.name)
	$("#groups>div").remove()
	$(".addobjs").removeClass("activeaddobjs")
	if(typeof(inst.groups)!="undefined") {
		$(".groups.noneyet").collapse("hide")
		$(".groups.somethere").collapse("show")
		for(group of inst.groups) {
			$("#groups").append('<div class="instobj" ><span class="objspan">'+group.id+'</span>: <span class="objspan">'+group.name+'</span> <i class="fas fa-trash text-danger float-right deletegroupparticipant"></i></div>')
		}
	} else 	{
		$(".groups.noneyet").collapse("show")
		$(".groups.somethere").collapse("hide")
	}
	$("#participants>div").remove()
	if(typeof(inst.participants)!="undefined") {
		$(".participants.noneyet").collapse("hide")
		$(".participants.somethere").collapse("show")
		for(participant of inst.participants) {
			$("#participants").append('<div class="instobj" ><span class="objspan">'+participant.id+'</span>: <span class="objspan">'+participant.name+'</span> <i class="fas fa-trash text-danger float-right deletegroupparticipant"></i></div>')
		}
	} else 	{
		$(".participants.noneyet").collapse("show")
		$(".participants.somethere").collapse("hide")
	}
	$(".objspan").attr("contenteditable",true)//click(editgroupparticipant)
	$(".deletegroupparticipant").click(deletegroupparticipant)
	$("#instmodal").modal("show")
}
function saveinstitution() {
	var inst={id:$("#instid").val(),name:$("#instname").val(),groups:getinstobj("groups"),participants:getinstobj("participants")}
	var instnum=$("#instnum").val()
	institutions[instnum]=inst
	send("updatejson","donothing",{study_id:$("#study_id").val(),update:true,col:"institutions",whereandvals:['$['+instnum+']',inst]},"backend")
	$("#instmodal").modal("hide")
}
function getinstobj(o) {
	return $("#"+o).find(".instobj").map(function(i) {return {id:$(this).children(".objspan:first").text(),name:$(this).children(".objspan:last").text()}}).get()
}
function deleteinst() {
	if(window.confirm(_('Do you really want to delete this institution and all participants and groups in it?'))) {
		var div=$(this).closest(".instobj")
		var pool=$(this).closest(".pool")
		var instnum=div.data("instnum")
		div.nextAll(".instobj").each(function() {$(this).data("instnum",Number($(this).data("instnum"))-1)})
		div.remove()
		institutions.splice(instnum,1)
		send("updatejson","donothing",{study_id:$("#study_id").val(),deleteelem:true,col:'institutions',whereandvals:['$['+instnum+']']},"backend")
		if(pool.children().length==0) {
			pool.siblings('.noneyet').collapse("show")
			pool.siblings('.somethere').collapse("hide")
		}
	}
}
function deleteobserver() {
	if(window.confirm(_('Do you want to delete this observer from the study?'))) {
		var observer=$(this).closest(".observer")
		var pool=$(this).closest(".pool")
		send("deleteobserver","donothing",{study_id:$("#study_id").val(),isinvited:observer.data("userid")==0,user:observer.data("userid")==0?observer.text():observer.data("userid")},"backend")
		observer.remove()
		if(pool.children().length==0) {
			pool.siblings('.noneyet').collapse("show")
			pool.siblings('.somethere').collapse("hide")
		}
	}
}
function deletesubject() {
	var pool=$(this).closest(".pool")
	var subjectnum=$(this).parent().index()
	$(this).parent().remove()
	if(pool.children().length==0) {
		pool.siblings('.noneyet').collapse("show")
		pool.siblings('.somethere').collapse("hide")
	}
	send("updatejson","donothing",{study_id:$("#study_id").val(),deleteelem:true,col:'subjects',whereandvals:['$['+subjectnum+']']},"backend")
}
function deletegroupparticipant() {
	var pool=$(this).closest(".pool")
	$(this).parent().remove()
	if(pool.children().length==0) {
		pool.siblings('.noneyet').collapse("show")
		pool.siblings('.somethere').collapse("hide")
	}
}
function addtopool() {
	var addobjs=$(this).siblings(".addobjs")
	var pool=$(this).closest(".form-row").find(".pool")
	var trimmed=$.trim(addobjs.val()).replace("[\n\r]+","\n")
	if(trimmed.length==0) return
	var newobjs=trimmed.split("\n")
	var type=pool.attr("id")
	var i=institutions.length
	var addinst=addsubj=newobservers=[]
	for(newobj of newobjs) {
		var idname=newobj.split(";")
		if(idname.length>1) {
			var id=$.trim(idname[0])
			var name=$.trim(idname[1])
		}
		else { 
			var id=0
			var name=$.trim(idname[0])
		}
		switch(type) {
			case "institutions":
				if(id==0)
					id=Math.max(0,Math.max.apply(null,institutions.map(v=>Number(v.id))))+1
				institutions[institutions.length]={id:id,name:name}
				addinst[addinst.length]="$"
				addinst[addinst.length]={id:id,name:name}
				var obj=$('<div class="instobj" data-instnum="'+i+'"><span class="editinstitution">'+id+': '+name+'</span> <i class="fas fa-trash text-danger float-right deleteinst"></i></div>')
				obj.find(".deleteinst").click(deleteinst)
				obj.find(".editinstitution").click(editinstitution)
				i++
			break;
			case "subjects":
				var obj=$('<div class="instobj"><span class="subjectspan">'+name+'</span> <i class="fas fa-trash text-danger float-right deletesubject"></i></div>')
				obj.find(".subjectspan").click(editsubject)
				obj.find(".deletesubject").click(deletesubject)
				addsubj[addsubj.length]="$"
				addsubj[addsubj.length]=name

			break;
			case "participants":
			case "groups":
				if(id==0) 
					var id=Math.max(0,Math.max.apply(null,pool.find(".instobj>.objspan:first-child").map(function() {return Number($(this).text())}).get()))+1
				var obj=$('<div class="instobj"><span class="objspan">'+id+'</span>: <span class="objspan">'+name+'</span> <i class="fas fa-trash text-danger float-right deletegroupparticipant"></i></div>')
				obj.find(".objspan").attr("contenteditable",true)//click(editgroupparticipant)
				obj.find(".deletegroupparticipant").click(deletegroupparticipant)
			break;
			case "observers":
				newobservers[newobservers.length]=name
// 				var obj=$('<div class="instobj"><span class="objspan">'+name+'</span> <i class="fas fa-trash text-danger float-right deleteinvited"></i></div>')
// 				obj.find(".deleteinvited").click(deleteobserver)
			break;
		}
		pool.append(obj)
	}	
	if(type=="institutions") {
		send("updatejson","donothing",{study_id:$("#study_id").val(),arrayappend:true,col:"institutions",whereandvals:addinst},"backend")
	}
	if(type=="subjects") {
		send("updatejson","donothing",{study_id:$("#study_id").val(),arrayappend:true,col:"subjects",whereandvals:addsubj},"backend")
	}
	if(type=="observers") {
		showMessage('<i class="fas fa-spinner fa-spin"></i><br>'+_("Please wait while we invite new observers ..."),100000)
		$("#addobstxtarea,#addobsbtn").attr("disabled",true)
		send("addobservers","addobserverstopool",{study_id:$("#study_id").val(),newobservers:newobservers},"backend")
	} else 	addobjs.val("")
	pool.siblings('.noneyet').collapse("hide")
	pool.siblings('.somethere').collapse("show")
}
function addobserverstopool(json) {
	for(let [a,id] of Object.entries(json.accepted)) {
		var obj=$('<div class="observer '+(id==0?"invited":"")+'" data-userid="'+(id==0?a:id)+'"><span class="objspan" >'+a+'</span> <i class="fas fa-trash text-danger float-right deleteobserver"></i></div>')
		obj.find(".deleteobserver").click(deleteobserver)
		$("#observers").append(obj)
	}
	$("#addobstxtarea,#addobsbtn").attr("disabled",false)
	$("#addobstxtarea").val("")
	$("#basesystemMessage").collapse("hide")
}
function editsubject() {
	var input=$('<input value="'+$(this).text()+'">')
	$(this).html(input)
	$(this).unbind("click")
	input.focus()
	input.on("blur",subjectchanged)
}
function subjectchanged() {
	var span=$(this).parent()
	var subject=$(this).val()
	span.html(subject)
	span.click(editsubject)
	send("updatejson","donothing",{study_id:$("#study_id").val(),update:true,col:"subjects",whereandvals:["$["+span.parent().index()+"]",subject]},"backend")
}

//////////////////////////////////////////////////////// 
// 
// Translate

function whenLoaded_translate_observationsscheme() {
	$("#language").change(function() {
		var image_url="img/flags/"+$(this).val()+".png"
		$.get(image_url).done(function() { 
			$("#langflag").attr("src",image_url)
		}).fail(function() { 
			$("#langflag").attr("src",null)
		})
	})
	$(".savetranslation").click(function() {
		if($("#language").val()=="") {
			showWarning(_("You need to choose the target language of your translation"));
		} else {
			send("savetranslation","aftersavetranslation",{translation:$("#translationform").serialize()},"backend")
		}
	})
}
function aftersavetranslation() {
	get_template("observationschemes",{},"whenLoaded_observationschemes")
}
//////////////////////////////////////////////////////// 
// 
// Observation Schemes

function whenLoaded_observationschemes(json) {
	$("#newScheme").click(function() {get_template("edit_observationscheme",{id:0},"whenLoaded_edit_observationscheme")})
	$(".editscheme").click(function() {get_template("edit_observationscheme",{id:$(this).parent().data("obsid")},"whenLoaded_edit_observationscheme")})
	$(".deletescheme").click(function() {if(window.confirm(_('Do you really want to delete this observation scheme? It CANNOT be undone.'))) send("deletescheme","schemedone",{id:$(this).parent().data("obsid")},"backend")})
	$(".tryscheme").click(function() {get_template("observe",{id:$(this).parent().data("obsid"),previousPage:"observationschemes"},"whenLoaded_observe")})
	$(".translate").click(function() {get_template("translate_observationscheme",{id:$(this).closest("tr").data("obsid"),transid:$(this).data("transid")},"whenLoaded_translate_observationsscheme")})
	$(".obsthumb").click(function() {$("#imgsrc").attr("src",$(this).attr("src")); $("#showimage").modal("show")})
	$("#pimg").click(function() {$("#showimage").modal("hide")})
	$(".togglepublic").click(function() {send("togglepublic","schemedone",{id:$(this).parent().data("obsid")},"backend")})
	$(".changelanguage").click(function() {send("getlanguages","showlanguages",{obsid:$(this).parent().data("obsid")},"backend")})
	$('.bs-toggle').bootstrapToggle()
	$('.bs-toggle').change(function() {	get_template("observationschemes",{publicschemes:$("#usepublic:checked").length>0},"whenLoaded_observationschemes")})
	$('.clonescheme').click(function() { if(window.confirm(_("Are you sure you want to clone this scheme? You can use it as is in your own studies. When you clone a scheme, you can edit it to fit your own needs."))) send("clonescheme","schemedone",{id:$(this).parent().data("obsid")},"backend")})
}
function whenLoaded_edit_observationscheme(json) {
	obsid=json.obsid
	if(localStorage.getItem("obsscheme_"+obsid)!=null) {
		if(window.confirm(_("You have an auto-saved version of the observation scheme in your browser. Do you want to use this version?"))) {
			var obs=JSON.parse(localStorage.getItem("obsscheme_"+obsid))
			console.log(obs)
			json.name=obs.name
			json.description=obs.description
			json.obsscheme=obs.obsscheme
		} else localStorage.removeItem("obsscheme_"+obsid)
		//localStorage.getItem("obsscheme")
	}
	var b=$(newRowButton)
	b.click(newRow)
	$("#obsscheme").append(b)
	if(json.obsscheme=="")
		newRow({delegateTarget:b})
	else {
		$("#osname").val(json.name)
		$("#osref").val(json.reference)
		$("#osdescr").val(json.description)
		for(const row of json.obsscheme.rows) {
			var br=newRow({delegateTarget:b},row)
			for(const col of row.cols) {
				bc=newColumn({delegateTarget:br},col)
					for(const cat of col.cats) {
						newCategory({delegateTarget:bc},cat)
				}
			}
		}
		$(".dependant").each(function() {connectDepends($(this))})

	}
	$("#obsscheme").sortable({
      items: ".obsrow"
    }).disableSelection();
	$("#saveobsscheme").click(saveobsscheme)
	window.setTimeout(autosaveobsscheme,100*1000) // 100 seconds
	$(window).on("beforeunload",localsaveobsscheme)
	$(".nav-link").on("click",localsaveobsscheme)
	$("#adddependency").click(makeDependant)
	$("#quickcreate").click(quickcreate)
}
function quickcreate() {
	var quickdata=$("#quickdata").val().trim()
	var categories=trimAll(quickdata.split("\n"))
	for(acat of categories) {
		var categoryparts=trimAll(acat.split(":"))
		var values=trimAll(categoryparts[1].split(";"))
		var lastcol=$(".obsrow:last>.obscol:last>.newCategory")
		var name=categoryparts[0]
		var cat={name:name,
				 type:"list",//"text":"list",
				 vals:values,
				 color: "",
				 mandatory: false,
				 multiple: false,
				 textcolor: ""
		}
 		newCategory({delegateTarget:lastcol},cat)
	}
	$("#quickfill").modal("hide")
}
function trimAll(a) {
	return a.map(x=>x.trim())
}
function schemedone(info) {
	if(info.update!=true) info={publicschemes:$("#usepublic").is("checked")}
	get_template("observationschemes",info,"whenLoaded_observationschemes")
}
function buildObsschemeJSON() {
	var error, warning
	var categories=[]
	var obsscheme={
		obsid:obsid,
		rows:$("#obsscheme .obsrow").map(function() {
			var name=$(this).find(".rowHeader").text()
			if(name==$(this).find(".rowHeader").data("emptytxt")) name=""
			return {
				name:name,
				color:$(this).find(".rowHeader").classOverlap(colorClasses),
				textcolor:$(this).find(".rowHeader").classOverlap(textClasses),
				cols:$(this).find(".obscol").map(function() {
						var name=$(this).find(".colHeader").text()
						if(name==$(this).find(".colHeader").data("emptytxt")) name=""
						return {
							name:name,
							color:$(this).find(".colHeader").classOverlap(colorClasses),
							textcolor:$(this).find(".colHeader").classOverlap(textClasses),
							cats:$(this).find(".obscategory").map(function() {
									var footer=$(this).find(".categoryFooter")
									var name=$(this).find(".categoryHeader").text()
									if(name==$(this).find(".categoryHeader").data("emptytxt") || name=="") error=_("Categories must have a name. The observation scheme hasn't been saved.")
									if(categories.indexOf(name)!==-1) warning=_("Two or more categories have the same name. Their values will be combined during observations. The observation scheme HAS been saved.")
									categories[categories.length]=name
									return {
										name:name,
										dependsOn:$(this).data("dependsOn"),
										color:$(this).classOverlap(colorClasses),
										textcolor:footer.classOverlap(textClasses),
										multiple:footer.find(".changeMultiple").hasClass("fa-check-double"),
										mandatory:footer.find(".changeMandatory").hasClass("fa-exclamation"),
										type:(footer.find(".changeType").hasClass("fa-clipboard")?"text":"list"),
										vals:$(this).find(".valueText").map(function() {return $(this).text()}).get(),
										placeholdertext:$(this).find(".textareaText").val(),
										textareaheight:$(this).find(".textareaText").css("height")
									}
								}).get()
						}
					}).get()
			}
		}).get(),
		categories:categories
	}
	// translate dependsOn from text to numbers
	var cats=obsscheme.rows.map(function(row) {
		return row.cols.map(function(col) {
			return col.cats.map(function(cat) {
				return cat
			});
		}).flat();
	}).flat();
	obsscheme.rows=obsscheme.rows.map(function(row) {
		return Object.assign(row, row.cols.map(function(col) {
			return Object.assign(col,col.cats.map(function(cat) {
				if(typeof(cat.dependsOn)!="undefined") {
					return Object.assign(cat,{dependsOnNum:cat.dependsOn.map(function(d) {
						var dependsOnCat=obsscheme.categories.indexOf(d.category)
						var dependsOnVal=cats[dependsOnCat].vals.indexOf(d.value)
						return {
							dependsOnCat:dependsOnCat,
							dependsOnVal:dependsOnVal
						}
					})});
				} else return cat
			}));
		}));
	});

	return [obsscheme,warning,error]
}
function localsaveobsscheme() {
	let editedobsscheme,warning,error
		[editedobsscheme,warning,error]=buildObsschemeJSON()
		localStorage.setItem("obsscheme_"+obsid,JSON.stringify({obsscheme:editedobsscheme,name:$("#osname").val(),description:$("#osdescr").val(),reference:$("#osref").val()}))
}
function autosaveobsscheme() {
	if($("#obsscheme").length>0) {
		if(typeof(Storage)!="undefined") {
			autosaveHandler=window.setTimeout(autosaveobsscheme,100*1000) // 100 seconds
			localsaveobsscheme()
			console.log("autosaving")
	// 		console.log(obsscheme)
		} else showWarning(_('Your browser does not support auto-save. Please remember to save from time to time.'))
	}
}
function saveobsscheme() {
	let editedobsscheme,warning,error
	[editedobsscheme,warning,error]=buildObsschemeJSON()
	obsscheme=editedobsscheme
	if(error!=null) {
		showWarning(error)
	} else {
		if(warning!=null) {
			showWarning(warning)
		}
		resetautosave()
		localStorage.removeItem("obsscheme_"+obsid)
		send("saveobsscheme","takeScreenShot",{obsscheme:JSON.stringify(editedobsscheme),name:$("#osname").val(),description:$("#osdescr").val(),reference:$("#osref").val(),id:obsid},"backend")
	}
	//console.log(JSON.stringify( editedobsscheme))
}
function takeScreenShot(json) {
	obsid=json.id
	var s=$(sequencemarkup)
	$("#observe").append(s)
	for(const row of json.obsscheme.rows) {
		var r=obsRow(row,s.find(".sequenceBody"))
		for(const col of row.cols) {
			c=obsColumn(col,r)
				for(const cat of col.cats) {
					obsCategory(cat,c)
			}
		}
	}
	$(".dependant").addClass("show")
	sshot()
}
function sshot() {
	node=document.getElementsByClassName("sequenceBody")[0]
	domtoimage.toBlob(node)
    .then(function (blob) {
		var fd = new FormData();
		fd.append('data', blob);
		fd.append('obsid', obsid);
		$.ajax({
			type: 'POST',
			url: './backend/saveScreenShot.php',
			data: fd,
			processData: false,
			contentType: false,
			cache: false,
	 		dataType : "json",
			success: function(json) {
				if(json.warning)
					showError(json.warning);
				schemedone({update:true,obsid:obsid})
			},
			error: function( xhr, status, errorThrown ) {
				showError(_("There was a problem. Are you connected to the Internet?") );
				console.log( "Error: " + errorThrown );
				console.log( "Status: " + status );
				console.dir( xhr );
				console.timeEnd(log)
			}
		})
	})
	.catch(function (error) {
		console.error('oops, something went wrong!', error);
	});
}

function newRow(e,json) {
	var b=$(newColumnButton)
	b.click(newColumn)
	var newRow=$(row).append(b)
	var header=newRow.find(".rowHeader")
	newRow.insertBefore(e.delegateTarget)
	if(typeof(json)=="undefined") {
		editableText({delegateTarget:header})
		newColumn({delegateTarget:b})
	}
	else {
		insertSavedText(header,json.name)
		if(typeof(json.color)!="undefined") {
			header.addClass(json.color)
			header.addClass(json.textcolor)
		}
	}
	newRow.find(".deleteRow").click(deleteRow)
	newRow.sortable({
		connectWith: ".rowSortable",
		items: ".obscol"
	}).disableSelection();
	newRow.find(".changeColor").click(changeRowColColor)
	return b
}
function newColumn(e,json) {
	var b=$(newCategoryButton)
	b.click(newCategory)
	var newColumn=$(column).append(b)
	var header=newColumn.find(".colHeader")
	newColumn.insertBefore(e.delegateTarget)
	if(typeof(json)=="undefined") {
		editableText({delegateTarget:header})
		newCategory({delegateTarget:b})
	}
	else {
		insertSavedText(header,json.name)
		if(typeof(json.color)!="undefined") {
			header.addClass(json.color)
			header.addClass(json.textcolor)
		}
	}
	f=$(colFooter)
	newColumn.append(f)
	f.find(".deleteCol").click(deleteCol)

    newColumn.sortable({
      connectWith: ".columnSortable",
      items: ".obscategory"
    }).disableSelection();
	newColumn.find(".changeColor").click(changeRowColColor)
	return b
}
function newCategory(e,json) {
	var newCategory=$(category)
	var header=newCategory.find(".categoryHeader")
	var ul=getUL()
	newCategory.append(ul)
	footer=$(categoryFooter)
	newCategory.append(footer)
	// Set events
	footer.find(".changeColor").click(changeColor)
	footer.find(".changeMultiple").click(changeMultiple)
	footer.find(".changeMandatory").click(changeMandatory)
	footer.find(".changeType").click(changeType)
	footer.find(".makeDependant").click(editDependant)
	newCategory.insertBefore(e.delegateTarget)
	if(typeof(json)=="undefined") {
		editableText({delegateTarget:header})
		newValue({delegateTarget:ul.find(".newValue")})
	} else {
		insertSavedText(header,json.name)
		if(typeof(json.color)!="undefined") {
			newCategory.addClass(json.color)
			newCategory.find(".categoryHeader,.categoryFooter").addClass(json.textcolor)
		}
		if(json.multiple) footer.find(".changeMultiple").click()
		if(json.mandatory) footer.find(".changeMandatory").click()
		if(json.type=="text") {
			footer.find(".changeType").click()
			newCategory.find(".textareaText").val(json.placeholdertext)
			if(json.textareaheight!=null) newCategory.find(".textareaText").css("height",json.textareaheight)
		}
		else {
			for(val of json.vals) {
				newValue({delegateTarget:ul.find(".newValue")},val)
			}
		}
		if(typeof(json.dependsOn)!="undefined") {
			newCategory.data("dependsOn",json.dependsOn)
			newCategory.addClass("dependant")
		}
	}
	newCategory.find(".deleteCat").click(deleteCat)
    newCategory.find(".categorySortable" ).sortable({
      connectWith: ".categorySortable"
    }).disableSelection();
}
function getUL(category) {
	var ul=$(valuelist)
	var b=$(newValueButton)
	b.click(newValue)
	ul.append(b)
	return ul
}
function newValue(e,val=null) {
	var newValue=$(value)
	var valueText=newValue.children(".valueText")
	var deleteValIcon=$(deleteValMarkup)
	newValue.append(deleteValIcon)
	newValue.insertBefore(e.delegateTarget)
	deleteValIcon.click(deleteVal)
	if(val==null)
		editableText({delegateTarget:valueText})
	else insertSavedText(valueText,val)
}
function changeRowColColor(e) {
	var elem=$(e.delegateTarget).parent().siblings(".rowHeader,.colHeader")
	var thisClass=elem.classOverlap(colorClasses)
	var oldClass=colorClasses.indexOf(thisClass)
	var newClass=oldClass+1
	if(newClass==colorClasses.length) newClass=0
	elem.removeClass(colorClasses[oldClass]).addClass(colorClasses[newClass])
	elem.removeClass(textClasses[oldClass]).addClass(textClasses[newClass])
}
function changeColor(e) {
	var elem=$(e.delegateTarget).closest(".obscategory")
	var thisClass=elem.classOverlap(colorClasses)
	var oldClass=colorClasses.indexOf(thisClass)
	var newClass=oldClass+1
	if(newClass==colorClasses.length) newClass=0
	elem.removeClass(colorClasses[oldClass]).addClass(colorClasses[newClass])
	elem.find(".categoryHeader,.categoryFooter").removeClass(textClasses[oldClass]).addClass(textClasses[newClass])
}
function changeMandatory(e) {
	$(e.delegateTarget).toggleClass("fa-hand-holding fa-exclamation")
//	$(e.delegateTarget).children(".fa-ban").toggleClass("show")
	toggleTitle(e)
}
function changeMultiple(e) {
	$(e.delegateTarget).toggleClass("fa-check fa-check-double")
	toggleTitle(e)
}
function changeType(e) {
	$(e.delegateTarget).toggleClass("fa-clipboard fa-list-ul")
	toggleTitle(e)
	$(e.delegateTarget).closest(".categoryFooter").find(".changeMultiple").collapse($(e.delegateTarget).hasClass("fa-list-ul")?"show":"hide")
	var content=$(e.delegateTarget).closest(".categoryFooter").prev()
	content.replaceWith($(e.delegateTarget).hasClass("fa-list-ul")?getUL(): $(textarea))
	
}
function toggleTitle(e) {
	var elem=$(e.delegateTarget)
	var tit=elem.attr("title")
	elem.attr("title",elem.data("alttitle"))
	elem.data("alttitle",tit)
}
function editableText(e) {
	if(dontEditValueText==true) return;
	var elem=$(e.delegateTarget)
	elem.off("click")
	var txt=elem.text()
	if(txt==elem.data("emptytxt")) txt=""
	var input=$('<input type=text style="width:90%">')
	elem.html(input)
	input.val(txt)
	input.blur(insertText)
	input.keydown(keyCheck)
	input.data("oldText",txt)
	input.attr("placeholder",elem.data("emptytxt"))
	if(elem.hasClass("categoryHeader") || (elem.hasClass("valueText") && elem.parent().prev().length>0)) {
		setTimeout(function() {input.focus()},100)
 	}
}
function insertText(e) {
	if(insertingTextActive) return
	insertingTextActive=true
	var input=$(e.target)
	var parent=input.parent()
	var txt=input.val()
	if(txt=="" && parent.hasClass("valueText")) deleteVal({delegateTarget:input.parent().siblings(".deleteVal")})
	else {
		if(txt==parent.data("emptytxt") || txt=="") {
			txt=parent.data("emptytxt")
			parent.addClass("text-muted")
		} else parent.removeClass("text-muted")
		input.parent().click(editableText)
		input.parent().html(txt)
	}
	insertingTextActive=false
}
function insertSavedText(elem,txt) {
	if(txt==elem.data("emptytxt") || txt=="") {
		txt=elem.data("emptytxt")
		elem.addClass("text-muted")
	} else elem.removeClass("text-muted")
	elem.click(editableText)
	elem.html(txt)
}
function keyCheck(e) {
	switch(e.key) {
		case "Enter":
			var parent=$(e.delegateTarget).parent()
			insertText(e)
			if(e.shiftKey && parent.hasClass("valueText"))
				newValue({delegateTarget:parent.parent().siblings(".newValue")})
			break
		case "Escape":
			cancelEdit(e)
			break
		case "Tab":
			var next=$(e.target).find("input")
			if(next.length>0) {
				next.focus()
			} else {
				
			}
			break
	}
}
function cancelEdit(e) {
	insertingTextActive=false
	var input=$(e.delegateTarget)
	var txt=input.data("oldText")
	if(txt=="") {
		if($(e.target).parent().hasClass("valueText")) deleteVal({delegateTarget:input.parent().siblings(".deleteVal")})
		else txt=input.data("emptytxt")
	}
	input.parent().click(editableText)
	input.parent().html(txt)
}
function deleteVal(e) {
	$(e.delegateTarget).parent().remove()
}
function deleteCat(e) {
	if(window.confirm(_('Do you really want to delete this category and all values in it?')))
		$(e.delegateTarget).closest(".obscategory").remove()
}
function deleteCol(e) {
	if(window.confirm(_('Do you really want to delete this column and all categories and values in it?')))
		$(e.delegateTarget).closest(".obscol").remove()
}
function deleteRow(e) {
	if(window.confirm(_('Do you really want to delete this row and all columns, categories and values in it?')))
		$(e.delegateTarget).closest(".obsrow").remove()
}
function editDependant(e) {
	dependantCategory=e.delegateTarget.closest(".obscategory")
	var elem=$(dependantCategory)
	if(elem.data("dependsOn")!=null) {
		$("#dependencymodal").modal("show")
		var dependencies=elem.data("dependsOn")
		$("#dependencymodalbody").html("")
		for(dependency of dependencies) {
			var p=$('<p><span class="observefont">'+dependency.category+'</span> > '+dependency.value+'</p>')
			var d=$('<i class="fas fa-trash deleteicon deletedependency float-right "></i>')
			d.data("dependency",dependency)
			p.append(d)
			$("#dependencymodalbody").append(p)
		}
		$(".deletedependency").click(deletedependency)
	} else {
		makeDependant(e)
	}
}
function makeDependant(e) {
	$("#dependencymodal").modal("hide")
	showMessage(_('Choose which value this category\'s visibility should depend on'),3000)
	$(".obsvalue").click(realizeMakeDependant)
	dontEditValueText=true
	$(document).keydown(isEsc)
}
function deletedependency(e) {
	d=$(dependantCategory)
	elem=$(e.delegateTarget)
 	newdepends=d.data("dependsOn").filter(function(i) {return i!=elem.data("dependency")});
	if(newdepends.length>0)
		d.data("dependsOn",newdepends)
	else {
		d.removeData("dependsOn")
		d.removeClass("dependant")
	}
	
	// Reset all dependency-interactions
	$(".obsvalue").removeClass("dependsOn")
	$(".obsvalue").off( "mouseenter mouseleave" )
	$(".obscategory").off( "mouseenter mouseleave" )
	
	// Redo dependency-interactions
	$(".obscategory").each(function() { if($(this).data("dependsOn")!=null) connectDepends($(this))})
	elem.parent().remove()
}
function realizeMakeDependant(e) {
	dontEditValueText=false
	$("#basesystemMessage").hide()
	var newdep=[{category:$(e.delegateTarget).closest(".obscategory").children(".categoryHeader").text(),value:$(e.delegateTarget).text()}]
	$(dependantCategory).data("dependsOn",($(dependantCategory).data("dependsOn") || []).concat(newdep))
	$(dependantCategory).addClass("dependant")
	connectDepends($(dependantCategory),newdep)
//   	$(dependantCategory).toggleClass( "dependantOn "+$(dependantCategory).classOverlap(colorClasses), 1000, "linear")//,function() {$(this).toggleClass( "dependantOn "+classOverlap($(dependantCategory),colorClasses), 1000, "linear")}) ;
//  	$(e.delegateTarget).toggleClass( "dependsOn", true,1000, "linear",function() {$(this).toggleClass( "dependsOn", false,1000, "linear")}) ;
// 	$(dependantCategory).trigger("hover")
// 	$(e.delegateTarget).trigger("hover")
	$(".obsvalue").off("click")
	$(document).off("keydown")
}
function connectDepends(elem,data) {
	if(typeof(data)=="undefined") var data=elem.data("dependsOn")
	for(var dependson of data) {
		if(dependson.category!=null) {
			var cat=$(".categoryHeader").each(function() { 
				if($(this).text() == dependson.category) {
					$(this).closest(".obscategory").find(".obsvalue").each(function() { 
						if($(this).text() == dependson.value) {
							var dependanton=$(this)
							elem.hover(function() {dependanton.toggleClass( "dependsOn",true, 500, "linear")},function() {dependanton.toggleClass( "dependsOn",false, 500, "linear")})
							dependanton.hover(function() {c=elem.classOverlap(colorClasses);elem.toggleClass( "dependantOn "+c,true,500, "linear")},function() {if(elem.hasClass("dependantOn")) elem.toggleClass( "dependantOn "+c, false,500, "linear")})
// 							console.log(dependanton)
						}
					})
				}
			})
		}
	}
}
function isEsc(e) {
	if(e.key=="Escape") {
		$(".obsvalue").off("click")
		dontEditValueText=false
		$(document).off("keydown")
		showMessage(_("Dependance registration cancelled."),1000)
	}
}
function showlanguages(json) {
	var l=$('tr[data-obsid="' + json.obsid + '"]>.changelanguage');
	l.unbind("click")
	var oldlang=l.text()
	var s=$("<select>")
	s.change(changelanguage)
	for(var ln of json.languages) s.append('<option value="'+ln+'" '+(ln==oldlang?'selected="selected"':'')+'>'+_(ln)+'</option>')
	l.html(s)
}
function changelanguage() {
	send("changeobslanguage","schemedone",{id:$(this).closest("tr").data("obsid"),newlanguage:$(this).val()},"backend")
}

///////
// myUser
function whenLoaded_myUser() {
	$("#finish").data("pagetype","startobserve")
	editUserData()
}
