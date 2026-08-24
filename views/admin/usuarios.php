<?php require_once __DIR__ . '/../layouts/header_admin.php'; ?>

<div class="flex items-center justify-between mb-5 flex-wrap gap-3 fade-up">
  <div>
    <h1 class="text-xl font-extrabold text-slate-800">Usuarios</h1>
    <p class="text-sm text-slate-400">Gestión de accesos al sistema</p>
  </div>
  <button type="button" class="btn btn-primary"
          onclick="document.getElementById('modalCrearUsuario').classList.add('active')">
    <i class="fas fa-plus"></i> Nuevo usuario
  </button>
</div>

<!-- Tabla -->
<div class="card overflow-hidden fade-up">
  <div class="overflow-x-auto">
    <table class="data-table resp-table">
      <thead>
        <tr>
          <th>Usuario</th>
          <th>Email</th>
          <th>Rol</th>
          <th>Estado</th>
          <th class="text-right">Acc.</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($usuarios)):
          $icon = 'fa-users'; $message = 'No hay usuarios registrados'; $cols = 5;
          require __DIR__ . '/../components/table_empty.php'; ?>
        <?php else: foreach ($usuarios as $u): ?>
        <tr>
          <td data-label="Usuario">
            <div class="flex items-center gap-3">
              <div class="avatar avatar-sm"
                   style="background:linear-gradient(135deg,#7c3aed,#a78bfa)">
                <?= strtoupper(substr($u['nombre'] ?? '?', 0, 1)) ?>
              </div>
              <div>
                <p class="font-semibold text-slate-800 text-sm">
                  <?= htmlspecialchars($u['nombre'] . ' ' . ($u['apellido'] ?? '')) ?>
                </p>
                <p class="text-slate-400 text-xs">#<?= $u['id'] ?></p>
              </div>
            </div>
          </td>
          <td data-label="Email" class="text-sm text-slate-600"><?= htmlspecialchars($u['email']) ?></td>
          <td data-label="Rol">
            <?php
              $typeMap = ['administrador'=>'blue', 'recepcionista'=>'green', 'medico'=>'yellow'];
              $type = $typeMap[$u['rol']] ?? 'gray';
              $text = ucfirst($u['rol']);
            ?><?php require __DIR__ . '/../components/badge.php'; ?>
          </td>
          <td data-label="Estado">
            <?php if (!empty($u['locked_until']) && strtotime($u['locked_until']) > time()):
              $type = 'blocked'; $text = 'Bloqueado'; $icon = 'fa-lock'; ?><?php require __DIR__ . '/../components/badge.php'; ?>
            <?php else:
              $type = 'active'; $text = 'Activo'; $icon = 'fa-check'; ?><?php require __DIR__ . '/../components/badge.php'; ?>
            <?php endif; ?>
          </td>
          <td data-label="Acc.">
            <div class="flex items-center justify-end gap-2">
              <?php if (!empty($u['locked_until']) && strtotime($u['locked_until']) > time()): ?>
              <form method="POST" action="<?= APP_URL ?>/admin/usuarios/<?= $u['id'] ?>/desbloquear">
                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                <button type="submit" class="btn btn-sm btn-success">
                  <i class="fas fa-lock-open"></i> Desbloquear
                </button>
              </form>
              <?php endif; ?>
              <?php
                $emailUsuario = htmlspecialchars($u['email'], ENT_QUOTES);
                $urlUsuario   = APP_URL . '/admin/usuarios/' . $u['id'] . '/eliminar';
              ?>
              <button type="button" class="btn btn-icon btn-sm btn-danger"
                      onclick="openDeleteModal('<?= $urlUsuario ?>', 'Se eliminará el usuario <?= $emailUsuario ?>. Esta acción es irreversible.')">
                <i class="fas fa-trash"></i>
              </button>
            </div>
          </td>
        </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal crear usuario -->
<div id="modalCrearUsuario" class="modal-overlay">
  <div class="modal-box" style="max-width:480px">
    <div class="flex items-center justify-between mb-5">
      <h3 class="text-base font-extrabold text-slate-800">Nuevo usuario</h3>
      <button type="button" onclick="this.closest('.modal-overlay').classList.remove('active')"
              class="text-slate-400 hover:text-slate-600 transition text-lg">
        <i class="fas fa-times"></i>
      </button>
    </div>
    <form method="POST" action="<?= APP_URL ?>/admin/usuarios/crear" class="grid grid-cols-2 gap-4" data-loading>
      <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
      <div class="floating-group">
        <input type="text" name="nombre" class="input-field" placeholder=" " required>
        <label>Nombre</label>
      </div>
      <div class="floating-group">
        <input type="text" name="apellido" class="input-field" placeholder=" " required>
        <label>Apellido</label>
      </div>
      <div class="col-span-2 floating-group">
        <input type="email" name="email" class="input-field" placeholder=" " required>
        <label>Email</label>
      </div>
      <div class="floating-group">
        <input type="password" name="password" class="input-field" placeholder=" " required>
        <label>Contraseña</label>
      </div>
      <div>
        <label class="block text-xs font-semibold text-slate-500 mb-1.5">Rol</label>
        <select name="rol" class="input-field" required>
          <option value="recepcionista">Recepcionista</option>
          <option value="administrador">Administrador</option>
          <option value="medico">Médico</option>
        </select>
      </div>
      <div class="col-span-2 flex justify-end gap-3 pt-2">
        <button type="button" class="btn btn-secondary"
                onclick="document.getElementById('modalCrearUsuario').classList.remove('active')">Cancelar</button>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar</button>
      </div>
    </form>
  </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer_admin.php'; ?>
