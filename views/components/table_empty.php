<?php
$icon    = $icon    ?? 'fa-inbox';
$message = $message ?? 'No hay datos disponibles';
$cols    = $cols    ?? 1;
?>
<tr>
  <td colspan="<?= $cols ?>">
    <div class="empty-state">
      <i class="fas <?= $icon ?>"></i>
      <h3><?= $message ?></h3>
      <p>No hay registros para mostrar en esta sección</p>
    </div>
  </td>
</tr>
