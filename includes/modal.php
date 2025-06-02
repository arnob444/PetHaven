<?php
function renderModal($id, $title, $content, $confirmText = 'Confirm', $cancelText = 'Cancel') {
?>
<div id="<?php echo $id; ?>" class="modal" role="dialog" aria-labelledby="<?php echo $id; ?>-title">
    <div class="modal-content">
        <h2 id="<?php echo $id; ?>-title"><?php echo $title; ?></h2>
        <p><?php echo $content; ?></p>
        <button class="btn close-modal"><?php echo $cancelText; ?></button>
        <button class="btn confirm-modal"><?php echo $confirmText; ?></button>
    </div>
</div>
<?php } ?>