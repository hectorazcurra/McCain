-- ══════════════════════════════════════════════════════════════
--  MAYA · McCain Vendedores — Esquema MySQL
--  Tablas para vendedores registrados y log de consultas
-- ══════════════════════════════════════════════════════════════

-- Tabla de vendedores registrados McCain
CREATE TABLE IF NOT EXISTS mccain_vendedores (
    id               INT          AUTO_INCREMENT PRIMARY KEY,
    nombre           VARCHAR(255) NOT NULL,
    numero_whatsapp  VARCHAR(30)  NOT NULL,
    region           VARCHAR(100) DEFAULT NULL,
    estado           ENUM('activo','inactivo') NOT NULL DEFAULT 'activo',
    created_at       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY uq_numero  (numero_whatsapp),
    INDEX      idx_region (region),
    INDEX      idx_estado (estado),
    INDEX      idx_fecha  (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Tabla de consultas / interacciones con MAYA
CREATE TABLE IF NOT EXISTS mccain_consultas (
    id               INT          AUTO_INCREMENT PRIMARY KEY,
    vendedor_id      INT          DEFAULT NULL,
    numero_whatsapp  VARCHAR(30)  NOT NULL,
    tipo_consulta    VARCHAR(50)  NOT NULL DEFAULT 'otro',
                     -- valores: info_producto, ventajas, preparacion, pedidos,
                     --          certificaciones, soporte, registro, saludo, otro
    mensaje_usuario  TEXT         DEFAULT NULL,
    respuesta_bot    TEXT         DEFAULT NULL,
    created_at       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_vendedor (vendedor_id),
    INDEX idx_numero   (numero_whatsapp),
    INDEX idx_tipo     (tipo_consulta),
    INDEX idx_fecha    (created_at),

    CONSTRAINT fk_consulta_vendedor
        FOREIGN KEY (vendedor_id)
        REFERENCES  mccain_vendedores(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
