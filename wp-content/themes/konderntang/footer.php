<?php
/**
 * The footer template file
 *
 * @package KonDernTang
 * @since 1.0.0
 */
?>

<?php konderntang_get_component('footer'); ?>
<?php konderntang_get_component('back-to-top'); ?>
<?php konderntang_get_component('cookie-consent'); ?>

</div><!-- #page -->

<?php konderntang_get_component('search-modal'); ?>

<?php wp_footer(); ?>
</body>

</html>