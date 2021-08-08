var sequencemarkup='<div class="container-fluid sequence"><div class="row"><div class="col sequenceHeader"><input type="text" required pattern="[0-9]{2}:[0-9]{2}" class="sequenceTime float-right form-control timeSize" ></div></div><div class="row justify-content-center"><div class="col-auto"><div class="container-fluid sequenceBody "></div></div></div></div>'
var rowmarkup='<div class="row obsrow mx-auto"></div>'
var rowHeadermarkup='<div class="card-header rowHeader"></div>'
var columnmarkup='<div class="col p-0 obscol"></div>'
var columnHeadermarkup='<div class="card-header colHeader"></div>'
var categorymarkup='<div class="card obscategory"><div class="card-header categoryHeader " ></div></div>'
var valuelistmarkup='<ul class="list-group list-group-flush"></ul>'
var valuemarkup='<li class="list-group-item obsvalue"><span class="valueText"></span></li>'
var textareamarkup='<textarea class="textareavalue"></textarea>'
var previousPage
var obsname
var obsscheme
var autosaveHandler
$(function(){
	whenLoaded_frontpage()
})
function whenLoaded_observe(json) {
	previousPage=json.previousPage
	obsid=json.obsid
	obsscheme=json.obsscheme
	obsname=json.name
	
	// Translate
	if(json.translation!=null) {
		var nextk=0
		for(let [i,r] of Object.entries(json.translation)) {
			if(r.name!="")
				obsscheme.rows[i].name=r.name
			if(typeof(r.cols)!="undefined") {
				for(let [j,co] of Object.entries(r.cols)) {
					if(co.name!="")
						obsscheme.rows[i].cols[j].name=co.name
					if(typeof(co.cats)!="undefined") {
						for(let [k,ca] of Object.entries(co.cats)) {
							if(ca.name!="")	{
								obsscheme.rows[i].cols[j].cats[k].name=ca.name
								obsscheme.categories[nextk]=ca.name
							}
							nextk++
							if(typeof(ca.vals)!="undefined") {
								for(let [l,v] of Object.entries(ca.vals)) {
									if(v!="")
										obsscheme.rows[i].cols[j].cats[k].vals[l]=v
								}
							}
						}
					}
				}
			}
		}
	}
	var localinserted=false
	if(localStorage.getItem("session_"+obsid)!=null) {
		if(window.confirm(_("You have auto-saved observations in your browser. Do you want to re-insert those?"))) {
			var session=JSON.parse(localStorage.getItem("session_"+obsid))
			institutioncontext=JSON.parse(localStorage.getItem("institutioncontext_"+obsid))
			populateSession(session)
			localinserted=true
		} else localStorage.removeItem("session_"+obsid)
	} 
	if(!localinserted) {
		var now=new Date()
		$("#sessionDate").val(now.getFullYear()+"-"+addzero(now.getMonth()+1)+"-"+addzero(now.getDate()))
		newsequence(true)
	}
	$("#saveobs").click(saveobs)
// 	$("#sshot").click(sshot)
	$("#download").click(download)
	$("#finish").click(observationfinished)
	$("#newsequence").click(newsequence)

	// Autosave
	window.setTimeout(autosaveobs,100*1000) // 100 seconds
	$(window).on("beforeunload",localsaveobs)
	$(".nav-link").on("click",localsaveobs)
}
function populateSession(session) {
	$("#sessionDate").val(session.date)
	for(var observation of session.observations) {
		var s=newsequence(true)
		s.find(".sequenceTime").val(observation.starttime)
		for (var i in ["date","starttime","endtime"]) delete observation[i]
		for(key in observation) {
			var cat=s.find(".obscategory:contains("+key+")")
			var val=observation[key]
			if(cat.find("textarea").length>0) cat.find("textarea").val(val) 
			else {
				if(!Array.isArray(val)) val=[val]
				for(v of val) {
					cat.find("li:contains("+v+")").click()
				}
			}
		}
	}
	// give the toggleClasses time to unfold...
	setTimeout(function() {testMandatory()},500)
}
function whenLoaded_frontpage(json) {
	$(".tryobserve").click(function() {get_template("observe",{id:$(this).data("obsid"),language:$(this).data("language"),previousPage:"frontpage"},"whenLoaded_observe")})
	$("#newinviteduser").click(function() {send("newinviteduser","newinviteduser",{name:$("#name").val(),username:$("#username").val(),email:$("#email").val(),registrationcode:$("#registrationcode").val(),password:$("#password").val()})})
}
function newinviteduser(json) {
	if(json.user_id>0) window.location.href="?backend=1"
}
function newsequence(donttestmandatory=false) {
	if(donttestmandatory!=true) 
		testMandatory()
	var s=$(sequencemarkup)
	var now=new Date()
	s.find(".sequenceTime").val(addzero(now.getHours())+":"+addzero(now.getMinutes()))
	$("#observe").append(s)
	for(const row of obsscheme.rows) {
		var r=obsRow(row,s.find(".sequenceBody"))
		for(const col of row.cols) {
			c=obsColumn(col,r)
				for(const cat of col.cats) {
					obsCategory(cat,c)
			}
		}
	}
	$(".dependant").each(function() {makeDepends($(this))})
	s.find(".obsvalue").click(valueclick)
	return s
}
function addzero(t) {
	return (t<10?"0":"")+String(t)
}
function testMandatory() {
	$(".needsvalue").removeClass("needsvalue")
	$(".mandatory").each(function() {
		var isSelected=$(this).find(".obsvalue").map(function() {return $(this).hasClass("valueSelected")}).get().indexOf(true)!==-1
		var isText=$(this).find(".textareavalue").map(function() {return $(this).val().length>0}).get().indexOf(true)!==-1
		if(!isSelected && !isText) $(this).addClass("needsvalue")
	})
}
function obsRow(row,s) {
	var m=$(rowmarkup)
	if(row.name!="") {
		var h=$(rowHeadermarkup)
		h.html(row.name)
		h.addClass(row.color)
		h.addClass(row.textcolor)
		m.append(h)
	}
	s.append(m)
	return m
}
function obsColumn(col,r) {
	var m=$(columnmarkup)
	if(col.name!="") {
		var h=$(columnHeadermarkup)
		h.html(col.name)
		h.addClass(col.color)
		h.addClass(col.textcolor)
		m.append(h)
	}
	r.append(m)
	return m
}
function obsCategory(cat,c) {
	var m=$(categorymarkup)
	m.children(".categoryHeader").html(cat.name)
	m.addClass(cat.color)
	m.find(".categoryHeader,.categoryFooter").addClass(cat.textcolor)
	if(cat.mandatory==true) m.addClass("mandatory")
	if(cat.multiple==true) m.addClass("multiple")
	if(cat.dependsOnNum!=null) {
		m.data("dependsOn",cat.dependsOnNum)
		m.addClass("dependant")
		m.addClass("collapse")
	}
	c.append(m)
	if(cat.type=="text") {
		var tm=$(textareamarkup)
		tm.attr("placeholder",cat.placeholdertext)
		if(cat.textareaheight!=null) tm.css("height",cat.textareaheight)
		tm.change(textareavalueChange)
		m.append(tm)
	} else {
		var ul=$(valuelistmarkup)
		for(l of cat.vals) {
			lm=$(valuemarkup)
			lm.html(l)
			ul.append(lm)
		}
		m.append(ul)
	}
}
var classChange = new MutationObserver(function(mutations) {
	//We just need the source, so if there is more than one mutation, just use the first one
	var source=$(mutations[0].target)
	var list=source.data("dependsOnThis")
	for(elem of list) {
		var dependantOnElems=elem.data("dependantOnElems")
		var show=false
		for(var dependson of dependantOnElems) {
			if(dependson.hasClass("valueSelected")) {
				show=true
				break
			}
		}
		elem.collapse(show?"show":"hide")
	}
})
function makeDepends(elem) {
	var data=elem.data("dependsOn")
	var dependantonList=[]
	for(var dependson of data) {
		var cat=elem.closest(".sequenceBody").find(".categoryHeader:nth("+dependson.dependsOnCat+")")
		var dependanton=cat.closest(".obscategory").find(".obsvalue:nth("+dependson.dependsOnVal+")")
		dependantonList.push(dependanton)
		var list=(dependanton.data("dependsOnThis")||[]).concat(elem)
		dependanton.data("dependsOnThis",list)
		classChange.observe($(dependanton)[0],{attributes: true,attributeFilter:["class"],attributeOldValue:true})
	}
	elem.data("dependantOnElems",dependantonList)
}


function valueclick(e) {
	$(this).toggleClass("valueSelected",500, "linear")
	if(!$(this).closest(".obscategory").hasClass("multiple")) {
		$(this).siblings().toggleClass("valueSelected",false,500, "linear")
	}
	$(this).closest(".obscategory").removeClass("needsvalue")
}

function textareavalueChange() {
	if($(this).val()!="") $(this).closest(".obscategory").removeClass("needsvalue")
}
function getObservations(endtime) {
	var st=$(".sequenceTime")
	var date=$("#sessionDate").val()
	return $(".sequence").map(function() {
		var et=$(this).index()
		var thisendtime=(et==(st.length-1)?endtime:$(st.get(et+1)).val())
		return Object.assign(
				{
					date:date,
					starttime:$(this).find(".sequenceTime").val(),
					endtime:thisendtime
				},
				...	$(this).find(".obscategory:not(.collapse:not(.show))").map(function() {
						return {
							[$(this).find(".categoryHeader").text()]:
								$(this).find(".textareavalue").length>0?
									$(this).find(".textareavalue").val():
									$(this).find(".obsvalue.valueSelected").map(function() {
										return $(this).text()
									}).get()
						}
				}).get()
			)
	}).get()
}
function getSession(endtime) {
	return {
		observations:getObservations(endtime),
		date:$("#sessionDate").val(),
		starttime:$(".sequenceTime").first().val(),
		endtime:endtime
	}
}
function localsaveobs() {
	var session=getSession(0)
	localStorage.setItem("session_"+obsid,JSON.stringify(session))
}
function autosaveobs() {
	if($("#observe").length>0) {
		if(typeof(Storage)!="undefined") {
			autosaveHandler=window.setTimeout(autosaveobs,100*1000) // 100 seconds
			localsaveobs()
			console.log("autosaving")
	// 		console.log(obsscheme)
		} else showWarning(_('Your browser does not support auto-save.'))
	}
}
function checkendtime() {
	var now=new Date()
	return window.prompt(_("Please check the end time of the session."),addzero(now.getHours())+":"+addzero(now.getMinutes()))
}
function saveobs() {
	
	if($(".needsvalue:not(.collapse:not(.show))").length>0) showWarning(_("Some values are missing. Look for the categories with red borders ...")) 
	else {
		var endtime=checkendtime()
		if(typeof(endtime)!="undefined") {
			var session=getSession(endtime)
			send("saveobservation","observationfinished",{session:session,institutioncontext:institutioncontext},"backend")
		}
	}
}
function resetautosave() {
	$(window).unbind("beforeunload")
	$(".nav-link").unbind("click",localsaveobs)
}
function download() {
	testMandatory()
	if($(".needsvalue:not(.collapse:not(.show))").length>0) showWarning(_("Some values are missing. Look for the categories with red borders ...")) 
	else {
		var endtime=checkendtime()
		if(typeof(endtime)!="undefined") {
			var observations=getObservations(endtime)
			var csv=Papa.unparse({fields:["date","starttime","endtime"].concat(obsscheme.categories),data:observations})
			var blob = new Blob([csv], {type: 'text/csv'});
			var d=new Date()
			var filename=obsname+d.toLocaleDateString(language.replace("_","-"))+".csv"
			if(window.navigator.msSaveOrOpenBlob) {
				window.navigator.msSaveBlob(blob, filename);
			}
			else{
				var elem = window.document.createElement('a');
				elem.href = window.URL.createObjectURL(blob);
				elem.download = filename;        
				document.body.appendChild(elem);
				elem.click();        
				document.body.removeChild(elem);
			}
			observationfinished()
		}
	}
}

function observationfinished() {
	resetautosave()
	get_template(previousPage,{},"whenLoaded_"+previousPage)
}
