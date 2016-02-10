$(function(){
	Dropzone.options.filedrop = {
	  init: function () {
		this.on("complete", function (file) {
		  if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
						location.reload();
		  }
		});
	  }
	};
	
	Dropzone.autoDiscover = false;

	var dropzone = new Dropzone (".dropzone",{
		addRemoveLinks: false // Don't show remove links on dropzone itself.
	});	
	
	dropzone.on("success", function(file) {
		this.removeFile(file);
	})	
	
    dropzone.on("complete", function (file) {
      if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
    	location.reload();
      }
    });	
});