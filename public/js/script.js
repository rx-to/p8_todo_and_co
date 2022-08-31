$("#category").on("change", function (e) {
	$(this).closest("form").submit(e);
    document.location = $(this).val();
});
