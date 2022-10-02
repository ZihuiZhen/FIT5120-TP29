var qi = 0;

// Wait for the document to load
document.addEventListener("DOMContentLoaded", () => {
	var questionList = document.querySelectorAll("[id^=question-]");

	//console.log("questions", questionList);
	reloop();

	function reloop() {
		for (let i = 0; i < questionList.length; i++) {
			questionList[i].classList.remove("active");
		}

		questionList[qi].classList.add("active");
		qi++;
		if (qi >= questionList.length) {
			qi = 0;
		}

		setTimeout(reloop, 3000);
	}
});
