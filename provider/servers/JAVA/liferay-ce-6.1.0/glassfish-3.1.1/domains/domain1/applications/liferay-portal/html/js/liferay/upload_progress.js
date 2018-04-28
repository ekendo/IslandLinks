function UploadProgress(uploadProgressId, redirect) {
	this.uploadProgressId = uploadProgressId;
	this.redirect = decodeURIComponent(redirect);
	this.count = 0;
	this.currentPercent = 0;
	this.currentSpeed = 0.1;
	this.startTime = 0;

	this.animateBar = UploadProgress_animateBar;
	this.hideProgress = UploadProgress_hideProgress;
	this.sendRedirect = UploadProgress_sendRedirect;
	this.startProgress = UploadProgress_startProgress;
	this.updateBar = UploadProgress_updateBar;
	this.updateIFrame = UploadProgress_updateIFrame;
	this.updateProgress = UploadProgress_updateProgress;
}

function UploadProgress_animateBar(percent) {
	this.count++

	percent = Math.max(percent, this.currentPercent);

	this.currentPercent = percent;

	var barContainer = document.getElementById(this.uploadProgressId + "-bar-div");
	var progressBar = document.getElementById(this.uploadProgressId + "-bar");
	var progressText = progressBar.getElementsByTagName("div")[1];

	barContainer.style.display = "block";

	if (percent < 100) {
		progressBar.style.width = percent + "%";
		progressText.innerHTML = Math.round(percent) + "%";

		setTimeout(this.uploadProgressId + ".animateBar(" + percent + ")", 100);
	}
	else {
		progressBar.style.width = "100%";
		progressText.innerHTML = Liferay.Language.get("done");
	}
}

function UploadProgress_hideProgress() {
	var barContainer = document.getElementById(this.uploadProgressId + "-bar-div");

	barContainer.style.display = "none";
}

function UploadProgress_sendRedirect() {
	window.location = this.redirect;
}

function UploadProgress_startProgress() {
	var barContainer = document.getElementById(this.uploadProgressId + "-bar-div");
	var timeLeftText = barContainer.getElementsByTagName("span")[0];

	var date = new Date();

	this.count = 0;
	this.currentPercent = 0;
	this.currentSpeed = 0.01;
	this.startTime = date.getTime();

	this.animateBar(0);

	setTimeout(this.uploadProgressId + ".updateProgress()", 1000);
}

function UploadProgress_updateBar(percent, filename) {
	this.currentPercent = percent;
}

function UploadProgress_updateProgress() {
	var uploadProgressPoller = document.getElementById(this.uploadProgressId + "-poller");

	uploadProgressPoller.src = themeDisplay.getPathMain() + "/portal/upload_progress_poller?uploadProgressId=" + this.uploadProgressId;
}

Liferay.provide(
	window,
	'UploadProgress_updateIFrame',
	function(height) {
		var A = AUI();

		var uploadPollerIFrame = document.getElementById(this.uploadProgressId + "-iframe");

		height += 40;

		var uploadIframe = A.one(uploadPollerIFrame).setStyle('height', height + 'px');

		var iframeBody = A.one(uploadPollerIFrame.contentWindow.document.body);

		iframeBody.replaceClass('portal-popup', 'portal-iframe');
		iframeBody.setStyle('height', height + 'px');
	},
	['aui-base']
);