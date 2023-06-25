<link href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/css/select2.css" rel="stylesheet"/>

<select class="osama">
    <option></option>
    <option value="AL">Alabama</option>
    <option value="AK">Alaska</option>
    <option value="AZ">Arizona</option>
</select>

<select class="osama">
    <option></option>
    <option value="AL">Alabama</option>
    <option value="AK">Alaska</option>
    <option value="AZ">Arizona</option>
</select>
<select class="osama">
    <option></option>
    <option value="AL">Alabama</option>
    <option value="AK">Alaska</option>
    <option value="AZ">Arizona</option>
</select>
{{--<select class="select2"></select>--}}

<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/select2.js"></script>

<script>
    var $select2 = $(".osama");

    // Copy the options to all selects based on the first one
    // $("select").html($select2.first().html());

    // Initialize Select2
    $select2.select2({
        allowClear: true,
        placeholder: "Select an option",
        tags: true
    });

    // Handle disabling already-selected options
    $select2.on("change", function () {
        $select2.find("option:disabled").prop("disabled", false).removeData("data");

        $select2.each(function () {
            var val = $(this).val();

            $select2.find("option:not(:selected)").filter(function () {
                return this.value == val;
            }).prop("disabled", true).removeData("data");
        });
    });
</script>
