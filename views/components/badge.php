<?php
$type  = $type  ?? 'gray';
$text  = $text  ?? '';
$icon  = $icon  ?? '';
$class = $class ?? '';

$colorMap = [
  'yellow'      => 'badge-yellow',
  'green'       => 'badge-green',
  'red'         => 'badge-red',
  'blue'        => 'badge-blue',
  'gray'        => 'badge-gray',
  'pending'     => 'badge-yellow',
  'pendiente'   => 'badge-yellow',
  'confirmed'   => 'badge-green',
  'confirmada'  => 'badge-green',
  'canceled'    => 'badge-red',
  'cancelada'   => 'badge-red',
  'completed'   => 'badge-blue',
  'completada'  => 'badge-blue',
  'atendida'    => 'badge-blue',
  'active'      => 'badge-green',
  'inactive'    => 'badge-red',
  'blocked'     => 'badge-red',
  'bloqueado'   => 'badge-red',
];
$badgeClass = $colorMap[$type] ?? 'badge-gray';
?>
<span class="badge <?= $badgeClass ?> <?= $class ?>">
  <?php if ($icon): ?><i class="fas <?= $icon ?> mr-1"></i><?php endif; ?>
  <?= $text ?>
</span>
