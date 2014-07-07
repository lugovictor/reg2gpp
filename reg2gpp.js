(function () {
	var visibleInstructions = null;
	
	function displayInstructions(section) {
		// Hide and show relevant div's containing instructions
		var thisDiv, thatDiv;
		if (section === "use") {
			thisDiv = document.getElementById("use-instructions-text");
			thatDiv = document.getElementById("apply-instructions-text");
		} else {
			thisDiv = document.getElementById("apply-instructions-text");
			thatDiv = document.getElementById("use-instructions-text");
		}
		if (visibleInstructions === section) {
			$(thisDiv).slideUp();
			thatDiv.style.display = "none"; // Just to make sure
			visibleInstructions = null;
		} else {
			$(thisDiv).slideDown();
			if (visibleInstructions !== null) {
				$(thatDiv).slideUp();
			}
			visibleInstructions = section;
		}
	}
	
	window.onload = function () {
		var useInstructDiv = document.getElementById("use-instructions");
		var applyInstructDiv = document.getElementById("apply-instructions");
		
		// Set up sliding div containers
		useInstructDiv.onclick = function () {
			displayInstructions("use");
		};
		applyInstructDiv.onclick = function () {
				displayInstructions("apply");
		};
		
		// Disable select inside div containers
		useInstructDiv.onselectstart = function () {
			return false;
		};
		applyInstructDiv.onselectstart = function () {
			return false;
		};
	}
	
}());