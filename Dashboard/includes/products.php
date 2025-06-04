<?php
$url = 'https://alexcg.de/autozone/api/cars.php';
$response = file_get_contents($url);
$data = json_decode($response, true);

$cars = $data['data'] ?? [];
$tipos = [];
$anios = [];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard de Autos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-light">
<div class="container my-5">
    <h1 class="text-center mb-4">Dashboard de Autos</h1>

    <div class="row mb-4">
        <div class="col-md-4">
            <select id="filtroTipo" class="form-select">
                <option value="">Todos los Tipos</option>
                <?php foreach ($cars as $c) {
                    $tipos[] = $c['type_name'];
                }
                foreach (array_unique($tipos) as $t) {
                    echo "<option value='$t'>$t</option>";
                } ?>
            </select>
        </div>
        <div class="col-md-4">
            <select id="filtroAnio" class="form-select">
                <option value="">Todos los Años</option>
                <?php foreach ($cars as $c) {
                    $anios[] = $c['year'];
                }
                foreach (array_unique($anios) as $a) {
                    echo "<option value='$a'>$a</option>";
                } ?>
            </select>
        </div>
    </div>

    <div class="row" id="autosContainer">
        <?php foreach ($cars as $car): ?>
            <div class="col-md-4 mb-3 auto" data-tipo="<?= $car['type_name'] ?>" data-anio="<?= $car['year'] ?>">
                <div class="card h-100">
                    <img src="<?= $car['image'] ?>" class="card-img-top" alt="<?= $car['model'] ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?= "{$car['brand']} {$car['model']} ({$car['year']})" ?></h5>
                        <p class="card-text"><?= $car['description'] ?></p>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><strong>Motor:</strong> <?= $car['motor'] ?></li>
                            <li class="list-group-item"><strong>Precio:</strong> $<?= $car['price'] ?></li>
                            <li class="list-group-item"><strong>Tipo:</strong> <?= $car['type_name'] ?></li>
                            <li class="list-group-item"><strong>Stock:</strong> <?= $car['stock'] ?> unidades</li>
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

    <!-- Modal de Actualización de Producto -->
    <div class="modal fade" id="updateProductModal" tabindex="-1" aria-labelledby="updateProductModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateProductModalLabel">Actualizar Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="updateProductForm">
                        <input type="hidden" id="update_product_id" name="id">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="update_name" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="update_name" name="name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="update_price" class="form-label">Precio</label>
                                <input type="number" class="form-control" id="update_price" name="price" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="update_brand" class="form-label">Marca</label>
                                <input type="text" class="form-control" id="update_brand" name="brand" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="update_model" class="form-label">Modelo</label>
                                <input type="text" class="form-control" id="update_model" name="model" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="update_year" class="form-label">Año</label>
                                <input type="number" class="form-control" id="update_year" name="year" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="update_mileage" class="form-label">Kilometraje</label>
                                <input type="number" class="form-control" id="update_mileage" name="mileage" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="update_description" class="form-label">Descripción</label>
                            <textarea class="form-control" id="update_description" name="description" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="update_image" class="form-label">Imagen</label>
                            <input type="file" class="form-control" id="update_image" name="image" accept="image/*">
                            <small class="text-muted">Deja vacío para mantener la imagen actual</small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="saveProductUpdate">Guardar Cambios</button>
                </div>
            </div>
        </div>
    </div>
</div>

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

// Gráfica de pastel
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

// Función para abrir el modal de actualización
function openUpdateModal(product) {
    document.getElementById('update_product_id').value = product.id;
    document.getElementById('update_name').value = product.name;
    document.getElementById('update_price').value = product.price;
    document.getElementById('update_brand').value = product.brand;
    document.getElementById('update_model').value = product.model;
    document.getElementById('update_year').value = product.year;
    document.getElementById('update_mileage').value = product.mileage;
    document.getElementById('update_description').value = product.description;
    
    const updateModal = new bootstrap.Modal(document.getElementById('updateProductModal'));
    updateModal.show();
}

// Manejar la actualización del producto
document.getElementById('saveProductUpdate').addEventListener('click', function() {
    const form = document.getElementById('updateProductForm');
    const formData = new FormData(form);
    
    // Convertir la imagen a base64 si se seleccionó una nueva
    const imageFile = formData.get('image');
    if (imageFile && imageFile.size > 0) {
        const reader = new FileReader();
        reader.onload = function(e) {
            formData.set('image', e.target.result);
            updateProduct(formData);
        };
        reader.readAsDataURL(imageFile);
    } else {
        formData.delete('image');
        updateProduct(formData);
    }
});

function updateProduct(formData) {
    const data = {};
    formData.forEach((value, key) => {
        data[key] = value;
    });

    // Aquí irá la URL de la API para actualizar el producto
    const apiUrl = 'URL_DE_LA_API_PARA_ACTUALIZAR_PRODUCTO';
    
    fetch(apiUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer ' + '<?php echo $_SESSION['token']; ?>'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            bootstrap.Modal.getInstance(document.getElementById('updateProductModal')).hide();
            loadProducts();
        } else {
            alert('Error al actualizar el producto: ' + (result.message || 'Error desconocido'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al actualizar el producto');
    });
}

function createProductCard(product) {
    return `
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <img src="${product.image}" class="card-img-top" alt="${product.name}" style="height: 200px; object-fit: cover;">
                <div class="card-body">
                    <h5 class="card-title">${product.name}</h5>
                    <p class="card-text">
                        <strong>Marca:</strong> ${product.brand}<br>
                        <strong>Modelo:</strong> ${product.model}<br>
                        <strong>Año:</strong> ${product.year}<br>
                        <strong>Kilometraje:</strong> ${product.mileage} km<br>
                        <strong>Precio:</strong> $${product.price}
                    </p>
                    <p class="card-text">${product.description}</p>
                </div>
                <div class="card-footer">
                    <button class="btn btn-primary btn-sm" onclick="openUpdateModal(${JSON.stringify(product)})">
                        <i class="material-icons">edit</i> Editar
                    </button>
                </div>
            </div>
        </div>
    `;
}
</script>
</body>
</html>
