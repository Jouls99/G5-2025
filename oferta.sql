CREATE TABLE ofertas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    descripcion TEXT,
    imagen_url VARCHAR(500),
    fecha_publicacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    activa TINYINT(1) DEFAULT 1
);