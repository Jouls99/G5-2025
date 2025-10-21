let reclamos = [];
let answers = {};
let nextId = 1;


function showPage(id){
  document.querySelectorAll('.container').forEach(c=>c.classList.remove('active'));
  document.getElementById(id).classList.add('active');

  // Si se muestra la página de inicio, cargar ofertas
  if (id === 'home') {
    cargarOfertasInicio();
  }
}

function selectAnswer(btn, question){
  const group = btn.parentNode.querySelectorAll('button');
  group.forEach(b=>b.classList.remove('selected'));
  btn.classList.add('selected');
  answers[question] = btn.innerText;
}

async function registrarReclamo(){
  const nombre = document.getElementById('nombre').value;
  const apellido = document.getElementById('apellido').value;
  const dni = document.getElementById('dni').value;
  const localidad = document.getElementById('localidad').value;
  const descripcion = document.getElementById('descripcion').value;

  if(!nombre||!apellido||!dni||!localidad||!descripcion){
    alert("Completa todos los campos"); return;
  }

  const data = {
    nombre, apellido, dni, localidad, descripcion,
    respuestas: {...answers}
  };

  try {
    const res = await fetch('registrar_reclamo.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(data)
    });
    const result = await res.json();

    if (result.success) {
      alert(result.message);
      // Limpiar formulario
      answers={};
      document.querySelectorAll('.btn-group button').forEach(b=>b.classList.remove('selected'));
      document.getElementById('nombre').value='';
      document.getElementById('apellido').value='';
      document.getElementById('dni').value='';
      document.getElementById('localidad').value='';
      document.getElementById('descripcion').value='';
      showPage('clienteMenu');
    } else {
      alert("Error: " + result.error);
    }
  } catch (error) {
    alert("Error de conexión con el servidor");
  }



  



}

async function cambiarEstado(select, id){
  const estado = select.value;

  try {
    const res = await fetch('actualizar_estado.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id, estado })
    });
    const result = await res.json();

    if (result.success) {
      alert(result.message);
    } else {
      alert("Error: " + result.error);
    }
  } catch (error) {
    alert("Error de conexión");
  }
}

async function buscarReclamo(){
  const filtro = document.getElementById('busqueda').value.trim();
  const url = filtro ? `listar_reclamos.php?q=${encodeURIComponent(filtro)}` : 'listar_reclamos.php';
  
  const container = document.getElementById('reclamosContainer');
  container.innerHTML = '<p>Buscando...</p>';

  try {
    const res = await fetch(url);
    const reclamos = await res.json();

    container.innerHTML = '';
    if (reclamos.length === 0) {
      container.innerHTML = '<p>No se encontraron reclamos.</p>';
      return;
    }

    reclamos.forEach(r => {
      const card = document.createElement('div');
      card.classList.add('reclamo-card');
      card.dataset.id = r.numero_ticket;
      card.dataset.dni = r.cliente_dni;

      let respuestasTxt = "Sin respuestas";
      if (r.respuestas && typeof r.respuestas === 'object') {
        respuestasTxt = Object.entries(r.respuestas)
          .map(([q,v]) => `${q}: ${v}`)
          .join(', ');
      }

      card.innerHTML = `
        <p><strong>Reclamo #${r.numero_ticket}</strong></p>
        <p><strong>Cliente:</strong> ${r.cliente_nombre} ${r.cliente_apellido}</p>
        <p><strong>DNI:</strong> ${r.cliente_dni}</p>
        <p><strong>Localidad:</strong> ${r.localidad_nombre}</p>
        <p><strong>Descripción:</strong> ${r.descripcion}</p>
        <p><strong>Preguntas técnicas:</strong> ${respuestasTxt}</p>
        <label>Estado:
          <select onchange="cambiarEstado(this,'${r.numero_ticket}')">
            <option ${r.estado=='Pendiente'?'selected':''}>Pendiente</option>
            <option ${r.estado=='En Proceso'?'selected':''}>En Proceso</option>
            <option ${r.estado=='Resuelto'?'selected':''}>Resuelto</option>
          </select>
        </label>
      `;
      container.appendChild(card);
    });
  } catch (error) {
    container.innerHTML = '<p>Error al buscar.</p>';
  }
}

async function eliminarReclamo(id) {
  if (!confirm("¿Seguro que desea eliminar este reclamo?")) return;

  try {
    // Creamos un objeto FormData para enviar datos como formulario
    const formData = new URLSearchParams();
    formData.append('id', id);

    const res = await fetch('eliminar_reclamo.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      body: formData
    });

    const result = await res.json();

    if (result.success) {
      alert("Reclamo eliminado correctamente");
      mostrarReclamos(); // Recarga la lista
    } else {
      alert("Error: " + result.error);
    }

  } catch (error) {
    alert("Error de conexión al eliminar reclamo: " + error.message);
  }
}

async function mostrarReclamos(){
  const container = document.getElementById('reclamosContainer');
  container.innerHTML = '<p>Cargando...</p>';

  try {
    const res = await fetch('listar_reclamos.php');
    const reclamos = await res.json();

    container.innerHTML = '';
    if (!reclamos || reclamos.length === 0) {
      container.innerHTML = '<p>No hay reclamos registrados.</p>';
      return;
    }

    reclamos.forEach(r => {
      const card = document.createElement('div');
      card.classList.add('reclamo-card');
      card.dataset.id = r.numero_ticket;
      card.dataset.dni = r.cliente_dni;

      // Formatear respuestas técnicas
      let respuestasTxt = "Sin respuestas";
      if (r.respuestas && typeof r.respuestas === 'object') {
        respuestasTxt = Object.entries(r.respuestas)
          .map(([q,v]) => `${q}: ${v}`)
          .join(', ');
      }



card.innerHTML = `
  <p><strong>Reclamo #${r.numero_ticket}</strong></p>
  <p><strong>Cliente:</strong> ${r.cliente_nombre || 'N/A'} ${r.cliente_apellido || 'N/A'}</p>
  <p><strong>DNI:</strong> ${r.cliente_dni || 'N/A'}</p>
  <p><strong>Localidad:</strong> ${r.localidad_nombre || 'N/A'}</p>
  <p><strong>Descripción:</strong> ${r.descripcion || 'Sin descripción'}</p>
  <p><strong>Preguntas técnicas:</strong> ${respuestasTxt}</p>
  <label>Estado:
    <select onchange="cambiarEstado(this,'${r.numero_ticket}')">
      <option ${r.estado=='Pendiente'?'selected':''}>Pendiente</option>
      <option ${r.estado=='En Proceso'?'selected':''}>En Proceso</option>
      <option ${r.estado=='Resuelto'?'selected':''}>Resuelto</option>
    </select>
  </label>
  <button class="delete-btn" onclick="eliminarReclamo('${r.numero_ticket}')">Eliminar Reclamo</button>
`;
container.appendChild(card);
    });
  } catch (error) {
    container.innerHTML = '<p>Error al cargar reclamos. Detalle: ' + error.message + '</p>';
  }
}






function mostrarTodos(){
  document.getElementById('busqueda').value='';
  document.querySelectorAll('#reclamosContainer .reclamo-card').forEach(c=>c.style.display="block");
}

async function consultarEstado(){
  const filtro = document.getElementById('consultaInput').value.trim();
  const resultado = document.getElementById('resultadoEstado');

  if (!filtro) {
    resultado.innerText = "Ingrese un número de reclamo o DNI";
    return;
  }

  try {
    const res = await fetch(`consultar_estado.php?q=${encodeURIComponent(filtro)}`);
    const data = await res.json();

    if (data.success) {
      resultado.innerText = `Reclamo #${data.numero_reclamo} - Estado: ${data.estado}`;
    } else {
      resultado.innerText = data.error;
    }
  } catch (error) {
    resultado.innerText = "Error de conexión con el servidor";
  }
}

async function agregarOferta() {
  const titulo = document.getElementById('ofertaTitulo').value.trim();
  const descripcion = document.getElementById('ofertaDescripcion').value.trim();
  const imagen = document.getElementById('ofertaImagen').files[0];
  const activa = document.getElementById('ofertaActiva').checked;

  if (!titulo) {
    alert("El título es obligatorio");
    return;
  }

  const formData = new FormData();
  formData.append('titulo', titulo);
  formData.append('descripcion', descripcion);
  formData.append('activa', activa ? 1 : 0);

  if (imagen) {
    formData.append('imagen', imagen);
  }

  try {
    const res = await fetch('agregar_oferta.php', {
      method: 'POST',
      body: formData
    });

    const result = await res.json();

    if (result.success) {
      alert(result.message);
      // Limpiar formulario
      document.getElementById('ofertaTitulo').value = '';
      document.getElementById('ofertaDescripcion').value = '';
      document.getElementById('ofertaImagen').value = '';
      document.getElementById('ofertaActiva').checked = true;
      mostrarOfertas(); // Recargar lista
    } else {
      alert("Error: " + result.error);
    }

  } catch (error) {
    alert("Error de conexión: " + error.message);
  }
}

async function mostrarOfertas() {
  const container = document.getElementById('ofertasContainer');
  container.innerHTML = '<p>Cargando ofertas...</p>';

  try {
    const res = await fetch('listar_ofertas.php');
    const ofertas = await res.json();

    container.innerHTML = '';

    if (!ofertas || ofertas.length === 0) {
      container.innerHTML = '<p>No hay ofertas activas.</p>';
      return;
    }

    ofertas.forEach(o => {
      const card = document.createElement('div');
      card.classList.add('reclamo-card');
      card.style.padding = '15px';
      card.style.margin = '10px 0';
      card.style.backgroundColor = '#f0f0f0';

      let imgHtml = '';
      if (o.imagen_url) {
        imgHtml = `<img src="${o.imagen_url}" alt="${o.titulo}" style="max-width: 100%; height: auto; margin-bottom: 10px;">`;
      }

      card.innerHTML = `
        <h3>${o.titulo}</h3>
        ${imgHtml}
        <p>${o.descripcion || 'Sin descripción'}</p>
        <p><small>Publicado: ${new Date(o.fecha_publicacion).toLocaleString()}</small></p>
        <button onclick="eliminarOferta(${o.id})" style="background-color: #ff4444; color: white; border: none; padding: 5px 10px; border-radius: 5px; cursor: pointer;">Eliminar</button>
      `;
      container.appendChild(card);
    });

  } catch (error) {
    container.innerHTML = '<p>Error al cargar ofertas: ' + error.message + '</p>';
  }
}

async function eliminarOferta(id) {
  if (!confirm("¿Seguro que desea eliminar esta oferta?")) return;

  try {
    const formData = new FormData();
    formData.append('id', id);

    const res = await fetch('eliminar_oferta.php', {
      method: 'POST',
      body: formData
    });

    const result = await res.json();

    if (result.success) {
      alert("Oferta eliminada correctamente");
      mostrarOfertas();
    } else {
      alert("Error: " + result.error);
    }

  } catch (error) {
    alert("Error de conexión: " + error.message);
  }
}

async function cargarOfertasInicio() {
  const container = document.getElementById('ofertasSlider');
  container.innerHTML = '<p>Cargando ofertas...</p>';

  try {
    const res = await fetch('listar_ofertas.php');
    const ofertas = await res.json();

    container.innerHTML = '';

    if (!ofertas || ofertas.length === 0) {
      container.innerHTML = '<p>No hay ofertas disponibles.</p>';
      return;
    }

    ofertas.forEach(o => {
      const card = document.createElement('div');
      card.style.padding = '10px';
      card.style.margin = '5px 0';
      card.style.border = '1px solid #ddd';
      card.style.borderRadius = '8px';
      card.style.backgroundColor = '#fff';

      let imgHtml = '';
      if (o.imagen_url) {
        imgHtml = `<img src="${o.imagen_url}" alt="${o.titulo}" style="max-width: 100%; height: auto; margin-bottom: 10px;">`;
      }

      card.innerHTML = `
        <h4>${o.titulo}</h4>
        ${imgHtml}
        <p>${o.descripcion || 'Sin descripción'}</p>
      `;
      container.appendChild(card);
    });

  } catch (error) {
    container.innerHTML = '<p>Error al cargar ofertas: ' + error.message + '</p>';
  }
}
