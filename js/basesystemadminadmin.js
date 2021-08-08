$(function() {
	$("#showMyObserve").click(showMyObserve);
});

function initObserve() {
	$(".editbasesystem").click(editbasesystem)
}


function showMyObserve() {
	get_template("mySystem",{contentdiv:"contentdiv"},"initObserve");
}


function editbasesystem() {
	switch($(this).attr("id")) {
		case "editOrgs":
			editOrganizationsIni()
		break;
		case "editUsers":
			editUsersIni()
		break
	}
}
function editUsersIni() {
	get_template("systemusers",{contentdiv:"basesystemcontentdiv"},"editUsers")	
}
function editOrganizationsIni() {
	get_template("organizations",{contentdiv:"basesystemcontentdiv"},"editOrganizations")
}
function editSportsIni() {
	get_template("sports",{contentdiv:"basesystemcontentdiv"},"editSports")
}
function editUsers() {
	//TODO:
 	$(".changePermissions").click(function() {if($(this).children().length==0) send("systemUserPermissions","selectUserPermissions",{user:$(this).data("user")},"backend")})
 	$(".deleteuser").click(function() {send("orgUser","editUsersIni",{deleteuser:$(this).data("user")},"backend")})
	
 	$(".newpass").click(function() {if(window.confirm(_("Do you want to set a new, random password for this user?"))) send("systemUser","newpassset",{newpass:$(this).data("user")},"backend")})
	
 	$("#create").click(function() {send("systemUser","editOrgUsersIni",{create:$("#email").val(),password:md5($("#password").val())},"backend")})
}
function newpassset(json) {
	alert(_("New password: {0}",json.newpass))
}
function editOrganizations() {
	$("#doCreateOrg").click(function() {
			send("createOrg","editOrganizationsIni",{orgname:$("#orgname").val(),orgslogan:$("#orgslogan").val(),orgurl:$("#orgurl").val(),createorg:true},"backend");
	})
	$(".removeorg").click(function() {send("createOrg","editOrganizationsIni",{removeorg:$(this).data("org_id")},"backend")})

	$(".editorgusers").click(editThisOrgUsersIni)
	
}
function editSports() {
	$("#doCreateSport").click(function() {
			send("createSport","editSportsIni",{sportname:$("#sportname").val(),createsport:true},"backend");
	})
	$(".removesport").click(function() {send("createSport","editSportsIni",{removesport:$(this).data("sport_id")},"backend")})

	$(".editsportsettings").click(editsportsettings)
	
}
function editThisOrgUsersIni(json) {
	var org_id=(json.org_id?json.org_id:$(this).data("org_id"))
	get_template("orgusers",{org_id:org_id,contentdiv:"basesystemcontentdiv"},"editOrgThisUsers")	
}
function editOrgThisUsers(json) {
	var org_id=json.org_id
	$("#invite").click(function() {send("orgUser","editThisOrgUsersIni",{org_id:org_id,invite:$("#email").val()},"backend")})
	$(".remove").click(function() {send("orgUser","editThisOrgUsersIni",{org_id:org_id,remove:$(this).data("user")},"backend")})
	$("#create").click(function() {send("orgUser","editThisOrgUsersIni",{org_id:org_id,create:$("#email").val(),password:md5($("#password").val())},"backend")})
	
}
function editsportsettings() {
	var sport_id=$(this).data("sport_id")
	get_template("sportsettings",{sport_id:sport_id,contentdiv:"basesystemcontentdiv"},"setsettings")	

}
