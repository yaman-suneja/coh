<script>
    let el_check = "<?php echo (isset($is_active) && $is_active == true) ? 'true' : 'false'; ?>";
    if (el_check == 'false') {
        window.location.hash = "activation";
    }
</script>
</div>