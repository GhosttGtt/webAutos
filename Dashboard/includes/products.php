<?php
//Conexion a GET API @Ismael 
function apiCarSingle($id)
{
    $url = 'https://alexcg.de/autozone/api/cars_single.php?id=' . $id;
    //inicializar cURL 
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($curl);

    if ($response === false) {
        error_log('Error en la API de carro individual: ' . curl_error($curl));
        curl_close($curl);
        return null;
    }

    curl_close($curl);
    $data = json_decode($response, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log('Error al decodificar JSON de la API de carro individual: ' . json_last_error_msg());
        return null;
    }

    if (empty($data)) {
        error_log('La API de carro individual devolvió datos vacíos');
    }

    return $data;
}

function apiCarList()
{
    $url = 'https://alexcg.de/autozone/api/cars.php';
    //inicializar cURL 
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($curl);

    if ($response === false) {
        error_log('Error en la API de carros: ' . curl_error($curl));
        curl_close($curl);
        return [];
    }

    curl_close($curl);
    $data = json_decode($response, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log('Error al decodificar JSON de la API de carros: ' . json_last_error_msg());
        return [];
    }

    if (empty($data)) {
        error_log('La API de carros devolvió un array vacío');
    }

    return $data;
}
// Manejo de operaciones CRUD a través de API
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $api_url = 'https://alexcg.de/autozone/api/';

    if (isset($_POST['create'])) {
        $data = array(
            'brand' => $_POST['brand'],
            'model' => $_POST['model'],
            'price' => floatval($_POST['price']),
            'stock' => intval($_POST['stock'])
        );

        // Manejo de la imagen
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $target_dir = "uploads/cars/";
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            $extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
            $image_name = uniqid() . '.' . $extension;
            $target_file = $target_dir . $image_name;

            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $data['image'] = $target_file;
            }
        }

        $ch = curl_init($api_url . 'create_car.php');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        $response = curl_exec($ch);
        curl_close($ch);
    } elseif (isset($_POST['update'])) {
        $data = array(
            'id' => intval($_POST['id']),
            'brand' => $_POST['brand'],
            'model' => $_POST['model'],
            'price' => floatval($_POST['price']),
            'stock' => intval($_POST['stock'])
        );

        $ch = curl_init($api_url . 'update_car.php');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        $response = curl_exec($ch);
        curl_close($ch);
    } elseif (isset($_POST['delete'])) {
        $id = intval($_POST['id']);

        $ch = curl_init($api_url . 'delete_car.php?id=' . $id);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
    }
}

//Consulta utilizando API
$carList = apiCarList();
?>

<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" />
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons" />
<script src="https://unpkg.com/@mui/material@latest/umd/material-ui.development.js"></script>

<!-- Modal de Edición -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <h2>Editar Producto</h2>
        <form id="editProductForm" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" id="edit-id">
            <div class="form-group">
                <div class="input-group">
                    <input type="text" name="brand" id="edit-brand" placeholder="Marca" required>
                    <input type="text" name="model" id="edit-model" placeholder="Modelo" required>
                    <input type="number" name="price" id="edit-price" placeholder="Precio" step="0.01" required>
                    <input type="number" name="stock" id="edit-stock" placeholder="Stock" required>
                </div>
                <div class="file-input-container">
                    <input type="file" name="image" id="edit-image" accept="image/*" class="file-input">
                    <label for="edit-image" class="file-label">
                        <i class="material-icons">add_photo_alternate</i>
                        <span>Seleccionar Nueva Imagen (Opcional)</span>
                    </label>
                </div>
                <button type="submit" name="update" class="button-save-modal">
                    <span class="button__text">Guardar Cambios</span>
                    <span class="button__icon">
                        <svg class="svg" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5 13L9 17L19 7" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </span>
                </button>
            </div>
        </form>
        <button class="btn-secondary" onclick="closeEditModal()">
            <i class="material-icons">cancel</i> Cancelar
        </button>
    </div>
</div>

</script>
<link rel="stylesheet" href="assets/css/products.css">
<link rel="stylesheet" href="assets/css/modal.css">
<link rel="stylesheet" href="assets/css/products-section.css">

<div class="productos-crud">
    <!-- Formulario de creación -->
    <div class="crud-form">
        <h2>Gestionar Productos</h2>
        <form method="POST" class="product-form" enctype="multipart/form-data">
            <div class="form-group">
                <div class="input-group">
                    <input type="text" name="brand" placeholder="Marca" required>
                    <input type="text" name="model" placeholder="Modelo" required>
                    <input type="number" name="price" placeholder="Precio" step="0.01" required>
                    <input type="number" name="stock" placeholder="Stock" required>
                </div>
                <div class="file-input-container">
                    <input type="file" name="image" id="image" accept="image/*" class="file-input" required>
                    <label for="image" class="file-label">
                        <i class="material-icons">add_photo_alternate</i>
                        <span>Seleccionar Imagen</span>
                    </label>
                </div>
                <button type="submit" name="create" class="button-add">
                    <span class="button__text">Agregar Producto</span>
                    <span class="button__icon">
                        <i class="material-icons">add</i>
                    </span>
                </button>
            </div>
        </form>
    </div>

    <!-- Tabla de productos -->
    <div class="table-responsive">
        <table class="product-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Imagen</th>
                    <th>Marca</th>
                    <th>Modelo</th>
                    <th>Año</th>
                    <th>Motor</th>
                    <th>Tipo</th>
                    <th>Precio</th>
                    <th>Stock</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($carList)): ?>
                    <?php foreach ($carList as $row): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td>
                                <img src="<?php echo !empty($row['image']) ? $row['image'] : 'assets/img/no-img.svg'; ?>"
                                    alt="Imagen del producto"
                                    class="product-image">
                            </td>
                            <td>
                                <span class="view-mode"><?php echo $row['brand']; ?></span>
                                <input type="text" class="edit-mode" value="<?php echo $row['brand']; ?>" style="display: none;">
                            </td>
                            <td>
                                <span class="view-mode"><?php echo $row['model']; ?></span>
                                <input type="text" class="edit-mode" value="<?php echo $row['model']; ?>" style="display: none;">
                            </td>
                            <td>
                                <span class="view-mode"><?php echo $row['year']; ?></span>
                                <input type="number" class="edit-mode" value="<?php echo $row['year']; ?>" style="display: none;">
                            </td>
                            <td>
                                <span class="view-mode"><?php echo $row['motor']; ?></span>
                                <input type="text" class="edit-mode" value="<?php echo $row['motor']; ?>" style="display: none;">
                            </td>
                            <td>
                                <span class="view-mode"><?php echo $row['type_name']; ?></span>
                                <input type="text" class="edit-mode" value="<?php echo $row['type_name']; ?>" style="display: none;">
                            </td>
                            <td>
                                <span class="view-mode">Q<?php echo number_format($row['price'], 2); ?></span>
                                <input type="number" class="edit-mode" value="<?php echo $row['price']; ?>" style="display: none;" step="0.01">
                            </td>
                            <td>
                                <span class="view-mode"><?php echo $row['stock']; ?></span>
                                <input type="number" class="edit-mode" value="<?php echo $row['stock']; ?>" style="display: none;">
                            </td>
                            <td class="actions" style="vertical-align: middle;">
                                <form method="POST" style="display: flex; gap: 8px; align-items: center; justify-content: center; margin: 0;">
                                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                    <button type="button" class="button-edit" onclick="toggleEdit(this)" style="height: 40px;">
                                        <span class="button__text">Editar</span>
                                        <span class="button__icon">
                                            <svg class="svg" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M4 21H20" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                <path d="M12.5 5.5L18.5 11.5L8.5 21.5H2.5V15.5L12.5 5.5Z" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </span>
                                    </button>
                                    <button type="submit" name="update" class="button-save" style="display: none; height: 40px;"></button>
                                    <button class="button-delet" type="button-delet" onclick="showDeleteConfirmation(this.form)">
                                        <span class="button__text">Delete</span>
                                        <span class="button__icon">
                                            <svg class="svg" height="512" viewBox="0 0 512 512" width="512" xmlns="http://www.w3.org/2000/svg">
                                                <title></title>
                                                <path d="M112,112l20,320c.95,18.49,14.4,32,32,32H348c17.67,0,30.87-13.51,32-32l20-320" style="fill:none;stroke:#fff;stroke-linecap:round;stroke-linejoin:round;stroke-width:32px"></path>
                                                <line style="stroke:#fff;stroke-linecap:round;stroke-miterlimit:10;stroke-width:32px" x1="80" x2="432" y1="112" y2="112"></line>
                                                <path d="M192,112V72h0a23.93,23.93,0,0,1,24-24h80a23.93,23.93,0,0,1,24,24h0v40" style="fill:none;stroke:#fff;stroke-linecap:round;stroke-linejoin:round;stroke-width:32px"></path>
                                                <line style="fill:none;stroke:#fff;stroke-linecap:round;stroke-linejoin:round;stroke-width:32px" x1="256" x2="256" y1="176" y2="400"></line>
                                                <line style="fill:none;stroke:#fff;stroke-linecap:round;stroke-linejoin:round;stroke-width:32px" x1="184" x2="192" y1="176" y2="400"></line>
                                                <line style="fill:none;stroke:#fff;stroke-linecap:round;stroke-linejoin:round;stroke-width:32px" x1="328" x2="320" y1="176" y2="400"></line>
                                            </svg>
                                        </span>
                                    </button>
                                    <button type="submit" name="delete" class="btn-delete" style="display:none;"></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="10" class="text-center">No hay productos en stock.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal de Confirmación para Eliminar -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <h2>Confirmar Eliminación</h2>
        <p>¿Estás seguro de que deseas eliminar este producto?</p>
        <div class="modal-actions">
            <button id="confirmDelete" class="btn-delete">
                <i class="material-icons">delete</i> Eliminar
            </button>
            <button id="cancelDelete" class="btn-secondary">
                <i class="material-icons">cancel</i> Cancelar
            </button>
        </div>
    </div>
</div>

<!-- Modal de Éxito -->
<div id="successModal" class="modal">
    <div class="modal-content">
        <i class="material-icons success-icon">check_circle</i>
        <h2>¡Operación Exitosa!</h2>
        <p id="successMessage"></p>
        <button class="btn-primary" onclick="closeSuccessModal()">
            <i class="material-icons">close</i> Cerrar
        </button>
    </div>
</div>

<script>
    // Funciones para manejar los modales y alertas
    let currentDeleteForm = null;

    function showDeleteConfirmation(form) {
        currentDeleteForm = form;
        document.getElementById('deleteModal').style.display = 'flex';
    }

    function showSuccessMessage(message) {
        document.getElementById('successMessage').textContent = message;
        document.getElementById('successModal').style.display = 'flex';
    }

    function closeSuccessModal() {
        document.getElementById('successModal').style.display = 'none';
    }

    // Event Listeners
    document.getElementById('confirmDelete').addEventListener('click', function() {
        if (currentDeleteForm) {
            currentDeleteForm.submit();
        }
        document.getElementById('deleteModal').style.display = 'none';
    });

    document.getElementById('cancelDelete').addEventListener('click', function() {
        document.getElementById('deleteModal').style.display = 'none';
        currentDeleteForm = null;
    });

    // Función para edición @Isma
    function toggleEdit(button) {
        const row = button.closest('tr');
        const id = row.querySelector('td:nth-child(1)').textContent;
        const brand = row.querySelector('td:nth-child(3) .view-mode').textContent;
        const model = row.querySelector('td:nth-child(4) .view-mode').textContent;
        const price = row.querySelector('td:nth-child(8) .view-mode').textContent.replace('Q', '');
        const stock = row.querySelector('td:nth-child(9) .view-mode').textContent;

        document.getElementById('edit-id').value = id;
        document.getElementById('edit-brand').value = brand;
        document.getElementById('edit-model').value = model;
        document.getElementById('edit-price').value = parseFloat(price);
        document.getElementById('edit-stock').value = parseInt(stock);

        document.getElementById('editModal').style.display = 'flex';
    }

    function closeEditModal() {
        document.getElementById('editModal').style.display = 'none';
    }

    // Mosttar modal 
    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <?php if (isset($_POST['create'])): ?>
            showSuccessMessage('Producto agregado exitosamente');
        <?php elseif (isset($_POST['update'])): ?>
            showSuccessMessage('Producto actualizado exitosamente');
        <?php elseif (isset($_POST['delete'])): ?>
            showSuccessMessage('Producto eliminado exitosamente');
        <?php endif; ?>
    <?php endif; ?>
</script>