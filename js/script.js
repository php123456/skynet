$(document).ready(function (){

	$("#content").on("click", ".click1", function()
	{
        var arr = ["", "", ""];

        arr = $(this).attr("id").split('_');
        to_page(arr[1], arr[2]);

	});

	$("#content").on("click", "a", function(e)
	{

        var arr = ["", "", ""];

        arr = $(this).attr("id").split('_');

		to_page(arr[1], arr[2]);

		return false;
	});

	function to_page(page, id)
	{
   				function(data) {
          		$("#content").html(data);
   		});



})