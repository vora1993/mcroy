var Lock = function () {

    return {
        //main function to initiate the module
        init: function () {

             $.backstretch([
		        "../img/background/1.jpg",
    		    "../img/background/2.jpg",
    		    "../img/background/3.jpg",
    		    "../img/background/4.jpg"
		        ], {
		          fade: 1000,
		          duration: 8000
		      });
        }

    };

}();

jQuery(document).ready(function() {
    Lock.init();
});