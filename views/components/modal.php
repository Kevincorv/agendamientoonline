<?php
$id        = $id        ?? 'modal';
$title     = $title     ?? '';
$maxWidth  = $maxWidth  ?? '420px';
$submitUrl = $submitUrl ?? '';
$submitText= $submitText ?? 'Guardar';
$csrfToken = $csrfToken ?? '';
?>
<div id="<?= $id ?>" class="modal-overlay">
  <div class="modal-box" style="max-width:<?= $maxWidth ?>">
    <div class="flex items-center justify-between mb-5">
      <h3 class="text-base font-extrabold text-slate-800"><?= $title ?></h3>
      <button type="button"
              onclick="document.getElementById('<?= $id ?>').classList.remove('active')"
              class="text-slate-400 hover:text-slate-600 transition text-lg">
        <i class="fas fa-times"></i>
      </button>
    </div>

    <?php if ($submitUrl): ?>
    <form method="POST" action="<?= $submitUrl ?>" class="flex flex-col gap-4">
      <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
    <?php endif; ?>

    <?= $slot ?? '' ?>

    <?php if ($submitUrl): ?>
      <div class="flex justify-end gap-3 pt-1">
        <button type="button" class="btn-secondary"
                onclick="document.getElementById('<?= $id ?>').classList.remove('active')">
          Cancelar
        </button>
        <button type="submit" class="btn-primary">
          <i class="fas fa-save"></i> <?= $submitText ?>
        </button>
      </div>
    </form>
    <?php endif; ?>
  </div>
</div>
