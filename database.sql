USE tareas_db;

CREATE TABLE tareas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    completada TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO tareas(titulo) VALUES
    ('Aprender Linux'),
    ('Configurar NGINX'),
    ('Desplegar APP a producción');
