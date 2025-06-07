<?php
include 'obtener_token.php';

function apiCarsList() {
    $token = obtenerToken();
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://alexcg.de/autozone/api/cars.php');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $token
    ]);
    $response = curl_exec($ch);
    curl_close($ch);
    $data = json_decode($response, true);
    return $data['data'] ?? [];
}

function crearCar($carData) {
    $token = obtenerToken();
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://alexcg.de/autozone/api/cars_create.php');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $token
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($carData));
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

function updateCar($carData) {
    $token = obtenerToken();
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://alexcg.de/autozone/api/cars_edit.php');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $token
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($carData));
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

function deleteCar($carId) {
    $token = obtenerToken();
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://alexcg.de/autozone/api/cars_delete.php?id=' . $carId);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $token
    ]);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

$cars = apiCarsList();

$tipos = array_unique(array_column($cars, 'type_name'));
$anios = array_unique(array_column($cars, 'year'));
?>


<div class="container my-5">
    <h1 class="text-center mb-4">Dashboard de Autos</h1>
    <button type="button" class="material-button primary" id="openAddProductModal">Agregar Nuevo Auto</button>

    <!-- Filtros -->
    <div class="row mb-4">
        <div class="col-md-4">
            <select id="filtroTipo" class="form-select">
                <option value="">Todos los Tipos</option>
                <?php foreach ($tipos as $t): ?>
                    <option value="<?= htmlspecialchars($t) ?>"><?= htmlspecialchars($t) ?></option>
                <?php endforeach; ?>
            </select>

</div>
        <div class="col-md-4">
            <select id="filtroAnio" class="form-select">
                <option value="">Todos los Años</option>
                <?php foreach ($anios as $a): ?>
                    <option value="<?= htmlspecialchars($a) ?>"><?= htmlspecialchars($a) ?></option>
                <?php endforeach; ?>
            </select>

</div>

</div>

    <!-- Cards de Autos -->
    <div class="row" id="autosContainer">
        <?php foreach ($cars as $car): ?>
            <div class="col-md-4 mb-3 auto" data-tipo="<?= htmlspecialchars($car['type_name']) ?>" data-anio="<?= htmlspecialchars($car['year']) ?>">
                <div class="card h-100 product-card" style="cursor:pointer;">
                    <img src="<?= htmlspecialchars($car['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($car['model']) ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars("{$car['brand']} {$car['model']} ({$car['year']})") ?></h5>
                        <script type="application/json"><?= json_encode($car) ?></script>
                        <p class="card-text"><?= htmlspecialchars($car['description']) ?></p>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><strong>Motor:</strong> <?= htmlspecialchars($car['motor']) ?></li>
                            <li class="list-group-item"><strong>Precio:</strong> $<?= htmlspecialchars($car['price']) ?></li>
                            <li class="list-group-item"><strong>Tipo:</strong> <?= htmlspecialchars($car['type_name']) ?></li>
                            <li class="list-group-item"><strong>Stock:</strong> <?= htmlspecialchars($car['stock']) ?> unidades</li>
                        </ul>
            
</div>
        
</div>
    
</div>
        <?php endforeach; ?>

</div>

    <!-- Gráficas -->
    <div class="row mt-5">
        <div class="col-md-6">
            <canvas id="barChart"></canvas>

</div>
        <div class="col-md-6">
            <canvas id="pieChart"></canvas>

</div>

</div>



</div>

<!-- Scripts -->

<script>
document.querySelectorAll('#filtroTipo, #filtroAnio').forEach(select => {
    select.addEventListener('change', () => {
        const tipo = document.getElementById('filtroTipo').value;
        const anio = document.getElementById('filtroAnio').value;

        document.querySelectorAll('.auto').forEach(card => {
            const matchTipo = tipo === "" || card.dataset.tipo === tipo;
            const matchAnio = anio === "" || card.dataset.anio === anio;
            card.style.display = matchTipo && matchAnio ? 'block' : 'none';
        });
    });
});

const tipoData = <?= json_encode(array_count_values(array_column($cars, 'type_name'))) ?>;
const anioData = <?= json_encode(array_count_values(array_column($cars, 'year'))) ?>;

new Chart(document.getElementById("barChart"), {
    type: 'bar',
    data: {
        labels: Object.keys(tipoData),
        datasets: [{
            label: 'Cantidad por Tipo',
            data: Object.values(tipoData),
            backgroundColor: 'rgba(54, 162, 235, 0.7)'
        }]
    }
});

new Chart(document.getElementById("pieChart"), {
    type: 'pie',
    data: {
        labels: Object.keys(anioData),
        datasets: [{
            label: 'Distribución por Año',
            data: Object.values(anioData),
            backgroundColor: ['#ff6384','#36a2eb','#ffce56','#4bc0c0','#9966ff']
        }]
    }
});

    
</script>

<!-- Modal Actualizar Producto -->
<div id="updateProductModal" class="material-modal-content">
    <div class="material-modal-header">
        <h4>Actualizar Producto</h4>
        <button type="button" class="material-modal-close-button">&times;</button>
    </div>
    <div class="material-modal-body">
        <form id="updateProductForm">
            <input type="hidden" id="update_product_id" name="id">
            <div class="material-form-group">
                <input type="text" id="update_brand" name="brand" required placeholder=" ">
                <label for="update_brand">Marca</label>
            </div>
            <div class="material-form-group">
                <input type="text" id="update_model" name="model" required placeholder=" ">
                <label for="update_model">Modelo</label>
            </div>
            <div class="material-form-group">
                <input type="number" id="update_year" name="year" required placeholder=" ">
                <label for="update_year">Año</label>
            </div>

            <div class="material-form-group">
                <input type="text" id="update_motor" name="motor" required placeholder=" ">
                <label for="update_motor">Motor</label>
            </div>
            <div class="material-form-group">
                <input type="text" id="update_fuel" name="fuel" required placeholder=" ">
                <label for="update_fuel">Combustible</label>
            </div>
            <div class="material-form-group">
                <input type="number" id="update_price" name="price" required placeholder=" ">
                <label for="update_price">Precio</label>
            </div>
            <div class="material-form-group">
                <input type="number" id="update_type_id" name="type_id" required placeholder=" ">
                <label for="update_type_id">ID de Tipo</label>
            </div>
            <div class="material-form-group">
                <input type="number" id="update_stock" name="stock" required placeholder=" ">
                <label for="update_stock">Stock</label>
            </div>
            <div class="material-form-group">
                <textarea id="update_description" name="description" rows="3" required placeholder=" "></textarea>
                <label for="update_description">Descripción</label>
            </div>
            <div class="material-file-input-wrapper">
                <input type="file" id="update_image" name="image" accept="image/*">
                <div class="material-file-input-display">
                    <span class="material-file-input-button">Imagen</span>
                    <span class="material-file-input-text">Deja vacío para mantener la imagen actual</span>
                </div>
            </div>
        </form>
    </div>
    <div class="material-modal-footer">
        <button type="button" class="material-button flat red" id="deleteProduct">Eliminar</button>
            <button type="button" class="material-button flat" onclick="updateProductModal.close()">Cancelar</button>
        <button type="button" class="material-button" id="saveProductUpdate">Guardar Cambios</button>
    </div>
</div>

<!-- Modal Agregar Producto -->
<div id="addProductModal" class="material-modal-content">
    <div class="material-modal-header">
        <h4>Agregar Nuevo Producto</h4>
        <button type="button" class="material-modal-close-button">&times;</button>
    </div>
    <div class="material-modal-body">
        <form id="addProductForm">
            <div class="material-form-group">
                <input type="text" id="add_brand" name="brand" required placeholder=" ">
                <label for="add_brand">Marca</label>
            </div>
            <div class="material-form-group">
                <input type="text" id="add_model" name="model" required placeholder=" ">
                <label for="add_model">Modelo</label>
            </div>
            <div class="material-form-group">
                <input type="number" id="add_year" name="year" required placeholder=" ">
                <label for="add_year">Año</label>
            </div>

            <div class="material-form-group">
                <input type="number" id="add_price" name="price" required placeholder=" ">
                <label for="add_price">Precio</label>
            </div>
            <div class="material-form-group">
                <textarea id="add_description" name="description" rows="3" required placeholder=" "></textarea>
                <label for="add_description">Descripción</label>
            </div>
            <div class="material-file-input-wrapper">
                <input type="file" id="add_image" name="image" accept="image/*" required>
                <div class="material-file-input-display">
                    <span class="material-file-input-button">Imagen</span>
                    <span class="material-file-input-text">Seleccionar imagen</span>
                </div>
            </div>
            <div class="material-form-group">
                <input type="text" id="add_motor" name="motor" required placeholder=" ">
                <label for="add_motor">Motor</label>
            </div>
            <div class="material-form-group">
                <input type="text" id="add_type_name" name="type_name" required placeholder=" ">
                <label for="add_type_name">Tipo</label>
            </div>
            <div class="material-form-group">
                <input type="number" id="add_stock" name="stock" required placeholder=" ">
                <label for="add_stock">Stock</label>
            </div>
        </form>
    </div>
    <div class="material-modal-footer">
        <button type="button" class="material-button flat" onclick="addProductModal.close()">Cancelar</button>
            <button type="button" class="material-button" id="saveNewProduct">Guardar Producto</button>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const token = "<?php echo obtenerToken(); ?>";
        const updateProductModal = new MaterialModal('updateProductModal');

        window.openUpdateModal = function(product) {
            document.getElementById('update_product_id').value = product.id;
            document.getElementById('update_brand').value = product.brand;
            document.getElementById('update_model').value = product.model;
            document.getElementById('update_year').value = product.year;

            document.getElementById('update_motor').value = product.motor;
            document.getElementById('update_fuel').value = product.fuel;
            document.getElementById('update_price').value = product.price;
            document.getElementById('update_type_id').value = product.type_id;
            document.getElementById('update_stock').value = product.stock;
            document.getElementById('update_description').value = product.description;
            document.getElementById('update_image').value = ''; // Clear file input
            updateMaterialTextFields(); // Apply Material Design input styles
            updateProductModal.open();
        };

        document.getElementById('saveProductUpdate').addEventListener('click', function() {
            const form = document.getElementById('updateProductForm');
            const formData = new FormData(form);
            const imageFile = formData.get('image');
            updateProductModal.close();

            if (imageFile && imageFile.size > 0) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    updateProduct(formData, e.target.result);
                };
                reader.readAsDataURL(imageFile);
            } else {
                updateProduct(formData, null);
            }
        });

        async function updateProduct(formData, base64Image) {
            const id = formData.get('id');
            const carData = {
                id,
                brand: formData.get('brand'),
                model: formData.get('model'),
                year: parseInt(formData.get('year')),
                motor: formData.get('motor'),
                fuel: formData.get('fuel'),
                price: parseFloat(formData.get('price')),
                description: formData.get('description'),
                type_id: parseInt(formData.get('type_id')),
                stock: parseInt(formData.get('stock'))
            };

            try {
                const response = await fetch('https://alexcg.de/autozone/api/cars_edit.php', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + token
                    },
                    body: JSON.stringify(carData)
                });
                const result = await response.json();
                if (response.ok) {
                    if (base64Image) {
                        await updateImage(id, base64Image);
                    } else {
                        alert('Producto actualizado con éxito');
                        updateProductModal.close();
                        location.reload();
                    }
                } else {
                    console.error(result);
                    alert('Error al actualizar el producto: ' + (result.message || 'Error desconocido'));
                }
            } catch (err) {
                console.error(err);
                alert('Error al actualizar el producto');
            }
        }

        async function updateImage(id, base64Image) {
            const imageData = new FormData();
            imageData.append('id', id);
            imageData.append('image', base64Image);

            try {
                const response = await fetch('https://alexcg.de/autozone/api/cars_edit_photo.php', {
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + token
                    },
                    body: imageData
                });
                const result = await response.json();
                if (response.ok) {
                    alert('Imagen actualizada con éxito');
                    updateProductModal.close();
                    location.reload();
                } else {
                    console.error(result);
                    alert('Error al actualizar la imagen: ' + (result.message || 'Error desconocido'));
                }
            } catch (err) {
                console.error(err);
                alert('Error al actualizar la imagen');
            }
        }

        document.getElementById('deleteProduct').addEventListener('click', async function() {
            const id = document.getElementById('update_product_id').value;

            if (!confirm('¿Estás seguro de que deseas eliminar este producto?')) return;

            try {
                const response = await fetch('https://alexcg.de/autozone/api/cars_delete.php?id=' + id, {
                    method: 'DELETE',
                    headers: {
                        'Authorization': 'Bearer ' + token
                    }
                });
                const result = await response.json();
                if (response.ok) {
                    alert('Producto eliminado con éxito');
                    updateProductModal.close();
                    location.reload();
                } else {
                    console.error(result);
                    alert('Error al eliminar el producto: ' + (result.message || 'Error desconocido'));
                }
            } catch (err) {
                console.error(err);
                alert('Error al eliminar el producto');
            }
        });

        document.addEventListener('click', function(event) {
            const productCard = event.target.closest('.product-card');
            if (productCard) {
                const productData = JSON.parse(productCard.closest('.auto').querySelector('script[type="application/json"]').textContent);
                openUpdateModal(productData);
            }
        });


    });

    
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const addProductModal = new MaterialModal('addProductModal');

        document.getElementById('openAddProductModal').addEventListener('click', function() {
            document.getElementById('addProductForm').reset();
            updateMaterialTextFields();
            addProductModal.open();
        });

        document.getElementById('saveNewProduct').addEventListener('click', async function() {
            const form = document.getElementById('addProductForm');
            const formData = new FormData(form);
            const imageFile = formData.get('image');

            let base64Image = null;
            if (imageFile && imageFile.size > 0) {
                base64Image = await new Promise(resolve => {
                    const reader = new FileReader();
                    reader.onload = (e) => resolve(e.target.result);
                    reader.readAsDataURL(imageFile);
                });
            } else {
                alert('Por favor, selecciona una imagen para el producto.');
                return;
            }

            const carData = {
                brand: formData.get('brand'),
                model: formData.get('model'),
                year: parseInt(formData.get('year')),

                price: parseFloat(formData.get('price')),
                description: formData.get('description'),
                image: base64Image,
                type_id: parseInt(formData.get('type_id')),
                motor: formData.get('motor'),
                stock: parseInt(formData.get('stock'))
            };

            try {
                const response = await fetch('https://alexcg.de/autozone/api/cars.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + token 
                    },
                    body: JSON.stringify(carData)
                });
                const result = await response.json();
                if (response.ok) {
                    alert('Producto agregado con éxito');
                    addProductModal.close();
                    location.reload();
                } else {
                    console.error(result);
                    alert('Error al agregar el producto: ' + (result.message || 'Error desconocido'));
                }
            } catch (err) {
                console.error(err);
                alert('Error al agregar el producto');
            }
        });
    });

    
</script>