<?php
// ══════════════════════════════════════════════════
//  MAYA · Portal McCain — Configuración y datos mock
//  McCain Foodservice — Asistente de Vendedores
// ══════════════════════════════════════════════════

if (session_status() === PHP_SESSION_NONE) session_start();

define('MCCAIN_PASS_HASH', password_hash('demo2026', PASSWORD_BCRYPT));
define('MCCAIN_API_KEY',   'maya-demo-2026');

// ── Usuarios del portal ──────────────────────────
$MCCAIN_ADMIN = [
    'mccain' => [
        'hash'   => MCCAIN_PASS_HASH,
        'nombre' => 'Administrador McCain',
        'rol'    => 'admin',
        'email'  => 'admin@mccain.com.ar',
    ],
];

// ── Vendedores registrados ───────────────────────
// IDs 1-3: usuarios de prueba reales
// IDs 4-18: vendedores ficticios para demo
$MCCAIN_VENDEDORES = [
    1  => ['id'=>1,  'nombre'=>'Hector Azcurra',      'numero_whatsapp'=>'+573106512999',  'region'=>'Colombia',     'estado'=>'activo',   'created_at'=>'2026-03-01 09:00:00', 'updated_at'=>'2026-03-01 09:00:00'],
    2  => ['id'=>2,  'nombre'=>'Emerson Bezerra',      'numero_whatsapp'=>'+5511968951221', 'region'=>'São Paulo',    'estado'=>'activo',   'created_at'=>'2026-03-02 10:00:00', 'updated_at'=>'2026-03-02 10:00:00'],
    3  => ['id'=>3,  'nombre'=>'Hernan Repetto',       'numero_whatsapp'=>'+5491144043309', 'region'=>'Buenos Aires', 'estado'=>'activo',   'created_at'=>'2026-03-03 11:00:00', 'updated_at'=>'2026-03-03 11:00:00'],
    4  => ['id'=>4,  'nombre'=>'Lucía Fernández',      'numero_whatsapp'=>'+5491134100004', 'region'=>'Buenos Aires', 'estado'=>'activo',   'created_at'=>'2026-03-05 08:30:00', 'updated_at'=>'2026-03-05 08:30:00'],
    5  => ['id'=>5,  'nombre'=>'Matías Romero',        'numero_whatsapp'=>'+5491134100005', 'region'=>'Buenos Aires', 'estado'=>'activo',   'created_at'=>'2026-03-06 09:15:00', 'updated_at'=>'2026-03-06 09:15:00'],
    6  => ['id'=>6,  'nombre'=>'Carolina Pérez',       'numero_whatsapp'=>'+5491134100006', 'region'=>'Buenos Aires', 'estado'=>'activo',   'created_at'=>'2026-03-07 10:00:00', 'updated_at'=>'2026-03-07 10:00:00'],
    7  => ['id'=>7,  'nombre'=>'Santiago Gómez',       'numero_whatsapp'=>'+5491134100007', 'region'=>'Buenos Aires', 'estado'=>'activo',   'created_at'=>'2026-03-08 14:00:00', 'updated_at'=>'2026-03-08 14:00:00'],
    8  => ['id'=>8,  'nombre'=>'Valentina Cruz',       'numero_whatsapp'=>'+5493516100008', 'region'=>'Córdoba',      'estado'=>'activo',   'created_at'=>'2026-03-10 09:00:00', 'updated_at'=>'2026-03-10 09:00:00'],
    9  => ['id'=>9,  'nombre'=>'Martín Suárez',        'numero_whatsapp'=>'+5493516100009', 'region'=>'Córdoba',      'estado'=>'activo',   'created_at'=>'2026-03-12 11:30:00', 'updated_at'=>'2026-03-12 11:30:00'],
    10 => ['id'=>10, 'nombre'=>'Florencia Molina',     'numero_whatsapp'=>'+5493516100010', 'region'=>'Córdoba',      'estado'=>'activo',   'created_at'=>'2026-03-14 08:45:00', 'updated_at'=>'2026-03-14 08:45:00'],
    11 => ['id'=>11, 'nombre'=>'Diego Torres',         'numero_whatsapp'=>'+5493414100011', 'region'=>'Rosario',      'estado'=>'activo',   'created_at'=>'2026-03-15 09:30:00', 'updated_at'=>'2026-03-15 09:30:00'],
    12 => ['id'=>12, 'nombre'=>'Camila López',         'numero_whatsapp'=>'+5493414100012', 'region'=>'Rosario',      'estado'=>'activo',   'created_at'=>'2026-03-16 10:15:00', 'updated_at'=>'2026-03-16 10:15:00'],
    13 => ['id'=>13, 'nombre'=>'Ricardo Aguilar',      'numero_whatsapp'=>'+5493414100013', 'region'=>'Rosario',      'estado'=>'activo',   'created_at'=>'2026-03-18 13:00:00', 'updated_at'=>'2026-03-18 13:00:00'],
    14 => ['id'=>14, 'nombre'=>'Ana Belén Sosa',       'numero_whatsapp'=>'+5492616100014', 'region'=>'Mendoza',      'estado'=>'activo',   'created_at'=>'2026-03-20 09:00:00', 'updated_at'=>'2026-03-20 09:00:00'],
    15 => ['id'=>15, 'nombre'=>'Pablo Méndez',         'numero_whatsapp'=>'+5492616100015', 'region'=>'Mendoza',      'estado'=>'inactivo', 'created_at'=>'2026-03-22 10:30:00', 'updated_at'=>'2026-03-22 10:30:00'],
    16 => ['id'=>16, 'nombre'=>'Laura García',         'numero_whatsapp'=>'+5493424100016', 'region'=>'Santa Fe',     'estado'=>'activo',   'created_at'=>'2026-03-25 08:00:00', 'updated_at'=>'2026-03-25 08:00:00'],
    17 => ['id'=>17, 'nombre'=>'Tomás Herrera',        'numero_whatsapp'=>'+5493424100017', 'region'=>'Santa Fe',     'estado'=>'inactivo', 'created_at'=>'2026-03-27 14:00:00', 'updated_at'=>'2026-03-27 14:00:00'],
    18 => ['id'=>18, 'nombre'=>'Gabriela Ruiz',        'numero_whatsapp'=>'+5493814100018', 'region'=>'Tucumán',      'estado'=>'inactivo', 'created_at'=>'2026-04-01 09:30:00', 'updated_at'=>'2026-04-01 09:30:00'],
];

// ── Log de consultas MAYA (mock — 52 entradas) ───
$MCCAIN_CONSULTAS = [
    // ── Semana 1 (Mar 5-9) ─────────────────────
    ['id'=>1,  'vendedor_id'=>4,  'numero_whatsapp'=>'+5491134100004', 'tipo_consulta'=>'saludo',          'mensaje_usuario'=>'Hola MAYA, buenos días',                              'respuesta_bot'=>'¡Hola Lucía! Soy MAYA, asistente de McCain. ¿En qué te puedo ayudar hoy? 😊',                              'created_at'=>'2026-03-05 08:35:00'],
    ['id'=>2,  'vendedor_id'=>4,  'numero_whatsapp'=>'+5491134100004', 'tipo_consulta'=>'info_producto',   'mensaje_usuario'=>'¿Qué formatos tienen las papas fritas clásicas?',     'respuesta_bot'=>'Las papas fritas clásicas McCain están disponibles en bolsas de 1kg, 2.5kg y 9kg para food service.',    'created_at'=>'2026-03-05 08:42:00'],
    ['id'=>3,  'vendedor_id'=>5,  'numero_whatsapp'=>'+5491134100005', 'tipo_consulta'=>'certificaciones', 'mensaje_usuario'=>'¿Tienen certificación halal?',                        'respuesta_bot'=>'Sí, varios productos McCain cuentan con certificación Halal. Las wedges y la línea clásica están certificadas.','created_at'=>'2026-03-06 10:20:00'],

    // ── Semana 2 (Mar 10-16) ───────────────────
    ['id'=>4,  'vendedor_id'=>8,  'numero_whatsapp'=>'+5493516100008', 'tipo_consulta'=>'preparacion',     'mensaje_usuario'=>'¿A qué temperatura cocino las wedges en horno?',      'respuesta_bot'=>'Wedges en horno convector: 220°C por 18-22 minutos. En freidora: 175°C por 3-4 minutos.',                  'created_at'=>'2026-03-10 15:10:00'],
    ['id'=>5,  'vendedor_id'=>1,  'numero_whatsapp'=>'+573106512999',  'tipo_consulta'=>'saludo',          'mensaje_usuario'=>'Buenas tardes',                                       'respuesta_bot'=>'¡Buenas tardes, Hector! ¿En qué puedo ayudarte hoy? Consultame sobre productos, preparación o pedidos 🥔',   'created_at'=>'2026-03-10 15:30:00'],
    ['id'=>6,  'vendedor_id'=>1,  'numero_whatsapp'=>'+573106512999',  'tipo_consulta'=>'ventajas',        'mensaje_usuario'=>'¿Qué ventajas tiene McCain vs papa fresca?',           'respuesta_bot'=>'Ventajas clave: rendimiento 70-80%, sin estacionalidad, tecnología IQF, ahorro de tiempo hasta 40%.',    'created_at'=>'2026-03-10 15:35:00'],
    ['id'=>7,  'vendedor_id'=>9,  'numero_whatsapp'=>'+5493516100009', 'tipo_consulta'=>'pedidos',         'mensaje_usuario'=>'¿Cuál es el pedido mínimo para restaurantes?',        'respuesta_bot'=>'El pedido mínimo depende del distribuidor. Te recomiendo contactar a tu ejecutivo de cuenta McCain.',       'created_at'=>'2026-03-12 11:40:00'],
    ['id'=>8,  'vendedor_id'=>2,  'numero_whatsapp'=>'+5511968951221', 'tipo_consulta'=>'info_producto',   'mensaje_usuario'=>'¿Qué es la tecnología IQF?',                          'respuesta_bot'=>'IQF = Individual Quick Frozen. Cada papa se congela por separado preservando textura, sabor y nutrientes.',  'created_at'=>'2026-03-12 14:20:00'],
    ['id'=>9,  'vendedor_id'=>10, 'numero_whatsapp'=>'+5493516100010', 'tipo_consulta'=>'preparacion',     'mensaje_usuario'=>'¿Se pueden preparar en air fryer?',                   'respuesta_bot'=>'¡Sí! Air fryer: 200°C por 12-15 minutos, agitando a mitad del proceso. Queda crujiente con menos aceite.',  'created_at'=>'2026-03-14 09:00:00'],
    ['id'=>10, 'vendedor_id'=>3,  'numero_whatsapp'=>'+5491144043309', 'tipo_consulta'=>'ventajas',        'mensaje_usuario'=>'¿Las papas tienen conservantes artificiales?',        'respuesta_bot'=>'No, la mayoría de los productos McCain NO contienen conservantes artificiales. Se conservan por congelado IQF.','created_at'=>'2026-03-14 16:00:00'],
    ['id'=>11, 'vendedor_id'=>11, 'numero_whatsapp'=>'+5493414100011', 'tipo_consulta'=>'pedidos',         'mensaje_usuario'=>'Necesito info sobre precios y descuentos',             'respuesta_bot'=>'Para condiciones comerciales y descuentos, contactá directamente a tu ejecutivo de cuenta McCain.',          'created_at'=>'2026-03-15 09:45:00'],
    ['id'=>12, 'vendedor_id'=>6,  'numero_whatsapp'=>'+5491134100006', 'tipo_consulta'=>'info_producto',   'mensaje_usuario'=>'¿Qué diferencia hay entre wedges y clásicas?',        'respuesta_bot'=>'Wedges: gajos anchos con cáscara, textura rústica. Clásicas: corte fino sin cáscara, ideales para freidora.',  'created_at'=>'2026-03-16 10:30:00'],

    // ── Semana 3 (Mar 17-23) ───────────────────
    ['id'=>13, 'vendedor_id'=>2,  'numero_whatsapp'=>'+5511968951221', 'tipo_consulta'=>'certificaciones', 'mensaje_usuario'=>'¿El producto tiene certificación kosher?',             'respuesta_bot'=>'Sí, la línea clásica de papas fritas McCain cuenta con certificación Kosher.',                            'created_at'=>'2026-03-18 13:30:00'],
    ['id'=>14, 'vendedor_id'=>14, 'numero_whatsapp'=>'+5492616100014', 'tipo_consulta'=>'preparacion',     'mensaje_usuario'=>'¿Cómo evito que las papas queden blandas?',            'respuesta_bot'=>'Clave: freidora bien caliente (175-180°C), no sobrecargar la canasta, no descongelar antes de cocinar.',   'created_at'=>'2026-03-20 09:15:00'],
    ['id'=>15, 'vendedor_id'=>12, 'numero_whatsapp'=>'+5493414100012', 'tipo_consulta'=>'ventajas',        'mensaje_usuario'=>'¿Cuánto rinde una bolsa de 2.5kg?',                   'respuesta_bot'=>'Rendimiento 70-80%. Aprox. 25-30 porciones de 80g por bolsa. Mucho mejor que la papa fresca.',             'created_at'=>'2026-03-20 11:00:00'],
    ['id'=>16, 'vendedor_id'=>3,  'numero_whatsapp'=>'+5491144043309', 'tipo_consulta'=>'info_producto',   'mensaje_usuario'=>'¿Tienen versión crinkle cut?',                        'respuesta_bot'=>'¡Sí! El corte crinkle (ondulado) es parte de la línea clásica McCain. Muy popular en fast food.',           'created_at'=>'2026-03-22 10:45:00'],
    ['id'=>17, 'vendedor_id'=>13, 'numero_whatsapp'=>'+5493414100013', 'tipo_consulta'=>'pedidos',         'mensaje_usuario'=>'¿Cuánto demora la entrega?',                          'respuesta_bot'=>'Los tiempos de entrega son 24-72hs hábiles según zona y distribuidor.',                                   'created_at'=>'2026-03-23 14:30:00'],

    // ── Semana 4 (Mar 24-30) ───────────────────
    ['id'=>18, 'vendedor_id'=>1,  'numero_whatsapp'=>'+573106512999',  'tipo_consulta'=>'info_producto',   'mensaje_usuario'=>'¿Los hash browns son buenos para desayuno?',           'respuesta_bot'=>'¡Absolutamente! Hash Browns McCain: perfectos para desayuno en hoteles y brunchs. Freidora 175°C, 2-3 min.',  'created_at'=>'2026-03-25 08:50:00'],
    ['id'=>19, 'vendedor_id'=>7,  'numero_whatsapp'=>'+5491134100007', 'tipo_consulta'=>'ventajas',        'mensaje_usuario'=>'¿Cuántos años lleva McCain en el mercado?',            'respuesta_bot'=>'McCain lleva más de 60 años en el mercado global. Fundada en 1957 en Canadá, opera en más de 160 países.',  'created_at'=>'2026-03-25 15:20:00'],
    ['id'=>20, 'vendedor_id'=>4,  'numero_whatsapp'=>'+5491134100004', 'tipo_consulta'=>'preparacion',     'mensaje_usuario'=>'¿Cómo almaceno el producto correctamente?',            'respuesta_bot'=>'Almacenamiento: -18°C o menos, en packaging original. Abierto: consumir en 24-48hs. NUNCA recongelar.',    'created_at'=>'2026-03-26 09:00:00'],
    ['id'=>21, 'vendedor_id'=>10, 'numero_whatsapp'=>'+5493516100010', 'tipo_consulta'=>'certificaciones', 'mensaje_usuario'=>'¿Son sin gluten las papas fritas?',                   'respuesta_bot'=>'¡Sí! Las papas fritas clásicas McCain son naturalmente sin gluten. Verificá siempre el packaging específico.',  'created_at'=>'2026-03-26 11:30:00'],
    ['id'=>22, 'vendedor_id'=>5,  'numero_whatsapp'=>'+5491134100005', 'tipo_consulta'=>'pedidos',         'mensaje_usuario'=>'¿Tienen promo por compra en volumen?',                'respuesta_bot'=>'Para promociones por volumen, contactá a tu ejecutivo de cuenta McCain de la zona.',                       'created_at'=>'2026-03-27 10:15:00'],
    ['id'=>23, 'vendedor_id'=>16, 'numero_whatsapp'=>'+5493424100016', 'tipo_consulta'=>'soporte',         'mensaje_usuario'=>'No me funciona el material de POP prometido',          'respuesta_bot'=>'Para temas de materiales POP, contactá a tu ejecutivo de cuenta. Puedo ayudarte con info de productos.',   'created_at'=>'2026-03-28 14:00:00'],
    ['id'=>24, 'vendedor_id'=>9,  'numero_whatsapp'=>'+5493516100009', 'tipo_consulta'=>'info_producto',   'mensaje_usuario'=>'¿Tienen Smiles? Un cliente preguntó',                 'respuesta_bot'=>'¡Sí! McCain Smiles son papas con forma de carita, ideales para menú infantil. Muy populares en restaurantes familiares.',  'created_at'=>'2026-03-29 09:30:00'],
    ['id'=>25, 'vendedor_id'=>2,  'numero_whatsapp'=>'+5511968951221', 'tipo_consulta'=>'ventajas',        'mensaje_usuario'=>'¿Cuánto ahorro en tiempo usando McCain vs fresca?',   'respuesta_bot'=>'Con papa fresca el proceso toma 40-60 min/kg. Con McCain: 0. Ahorro de hasta 40% en tiempo de preparación.',  'created_at'=>'2026-03-30 10:00:00'],

    // ── Semana 5 (Mar 31 - Apr 4) ─────────────
    ['id'=>26, 'vendedor_id'=>6,  'numero_whatsapp'=>'+5491134100006', 'tipo_consulta'=>'preparacion',     'mensaje_usuario'=>'¿Las onion rings se pueden hornear?',                 'respuesta_bot'=>'Sí, Onion Rings McCain en horno: 220°C por 15-18 min, dando vuelta a la mitad. Freidora quedan mejor.',    'created_at'=>'2026-04-01 09:00:00'],
    ['id'=>27, 'vendedor_id'=>3,  'numero_whatsapp'=>'+5491144043309', 'tipo_consulta'=>'info_producto',   'mensaje_usuario'=>'¿Qué vida útil tiene el producto congelado?',          'respuesta_bot'=>'18 a 24 meses desde la fecha de fabricación, manteniendo el producto a -18°C.',                            'created_at'=>'2026-04-01 11:20:00'],
    ['id'=>28, 'vendedor_id'=>11, 'numero_whatsapp'=>'+5493414100011', 'tipo_consulta'=>'ventajas',        'mensaje_usuario'=>'¿Qué certificaciones de calidad tienen?',             'respuesta_bot'=>'Plantas certificadas ISO 22000. Productos con Kosher, Halal (líneas seleccionadas) y SENASA/ANVISA.',       'created_at'=>'2026-04-02 08:45:00'],
    ['id'=>29, 'vendedor_id'=>12, 'numero_whatsapp'=>'+5493414100012', 'tipo_consulta'=>'preparacion',     'mensaje_usuario'=>'¿Cómo preparo las papas steak cut?',                  'respuesta_bot'=>'Steak cut McCain: freidora 170°C por 4-5 min, horno 210°C por 22-25 min. Ideales con carnes.',             'created_at'=>'2026-04-02 14:30:00'],
    ['id'=>30, 'vendedor_id'=>4,  'numero_whatsapp'=>'+5491134100004', 'tipo_consulta'=>'pedidos',         'mensaje_usuario'=>'Necesito hacer un pedido urgente esta semana',         'respuesta_bot'=>'Para pedidos urgentes, contactá directamente a tu ejecutivo de cuenta o distribuidor autorizado McCain.',   'created_at'=>'2026-04-03 09:10:00'],
    ['id'=>31, 'vendedor_id'=>13, 'numero_whatsapp'=>'+5493414100013', 'tipo_consulta'=>'info_producto',   'mensaje_usuario'=>'¿Tienen mini roasters? ¿Para qué se usan?',           'respuesta_bot'=>'¡Sí! Mini Roasters: papas pequeñas precocidas para hornear. Perfectas para catering y buffets gourmet.',   'created_at'=>'2026-04-03 11:00:00'],
    ['id'=>32, 'vendedor_id'=>1,  'numero_whatsapp'=>'+573106512999',  'tipo_consulta'=>'ventajas',        'mensaje_usuario'=>'¿Tienen argumentos para convencer a un chef?',        'respuesta_bot'=>'Para chefs: consistencia garantizada, ahorro en mise en place, sin desperdicio, variedad en un solo proveedor.', 'created_at'=>'2026-04-04 08:30:00'],
    ['id'=>33, 'vendedor_id'=>8,  'numero_whatsapp'=>'+5493516100008', 'tipo_consulta'=>'certificaciones', 'mensaje_usuario'=>'¿Necesito declarar que uso papa congelada en el menú?','respuesta_bot'=>'Las regulaciones varían por país. Consultá con el equipo técnico McCain para tu región.',                   'created_at'=>'2026-04-04 10:45:00'],
    ['id'=>34, 'vendedor_id'=>5,  'numero_whatsapp'=>'+5491134100005', 'tipo_consulta'=>'soporte',         'mensaje_usuario'=>'Un cliente dice que las papas quedaron aceitosas',     'respuesta_bot'=>'Papas aceitosas: temperatura del aceite muy baja. Debe ser 175-180°C. No agregar demasiadas a la vez.',     'created_at'=>'2026-04-05 09:20:00'],
    ['id'=>35, 'vendedor_id'=>2,  'numero_whatsapp'=>'+5511968951221', 'tipo_consulta'=>'info_producto',   'mensaje_usuario'=>'¿Las papas son de origen local?',                     'respuesta_bot'=>'McCain trabaja con papa local en los países donde opera, apoyando la cadena de valor regional.',             'created_at'=>'2026-04-05 11:30:00'],

    // ── Semana 6 (Apr 6-9) — mayor actividad ──
    ['id'=>36, 'vendedor_id'=>3,  'numero_whatsapp'=>'+5491144043309', 'tipo_consulta'=>'preparacion',     'mensaje_usuario'=>'¿Cuánto aceite absorben vs papa fresca?',              'respuesta_bot'=>'Gracias a IQF y el precocido, las papas McCain absorben 15-20% menos aceite que la papa fresca cortada.',  'created_at'=>'2026-04-06 08:30:00'],
    ['id'=>37, 'vendedor_id'=>7,  'numero_whatsapp'=>'+5491134100007', 'tipo_consulta'=>'ventajas',        'mensaje_usuario'=>'¿Por qué McCain y no otra marca?',                    'respuesta_bot'=>'McCain: 60+ años de experiencia, tecnología IQF propia, red de productores locales, soporte comercial completo.', 'created_at'=>'2026-04-06 09:15:00'],
    ['id'=>38, 'vendedor_id'=>14, 'numero_whatsapp'=>'+5492616100014', 'tipo_consulta'=>'pedidos',         'mensaje_usuario'=>'¿Tienen distribución en Mendoza?',                    'respuesta_bot'=>'Sí, McCain tiene distribución en toda la región de Cuyo. Contactá al ejecutivo de cuenta de tu zona.',       'created_at'=>'2026-04-06 10:00:00'],
    ['id'=>39, 'vendedor_id'=>4,  'numero_whatsapp'=>'+5491134100004', 'tipo_consulta'=>'info_producto',   'mensaje_usuario'=>'¿Las croquetas de papa son fáciles de vender?',        'respuesta_bot'=>'¡Las Croquetas McCain son un éxito! Ideales para bares y catering, preparación rápida y buena presentación.',  'created_at'=>'2026-04-06 14:20:00'],
    ['id'=>40, 'vendedor_id'=>1,  'numero_whatsapp'=>'+573106512999',  'tipo_consulta'=>'preparacion',     'mensaje_usuario'=>'¿Cuál es el tiempo de cocción de los hash browns?',   'respuesta_bot'=>'Hash Browns: freidora 175°C 2-3 min; plancha 3-4 min por lado; horno 200°C 12-15 min. Dorados y crujientes.', 'created_at'=>'2026-04-07 08:45:00'],
    ['id'=>41, 'vendedor_id'=>6,  'numero_whatsapp'=>'+5491134100006', 'tipo_consulta'=>'ventajas',        'mensaje_usuario'=>'¿Tienen soporte para lanzamiento en nuevo restaurant?', 'respuesta_bot'=>'¡Sí! McCain ofrece soporte: materiales de marketing, capacitación de cocina y ejecutivo de cuenta dedicado.',  'created_at'=>'2026-04-07 10:30:00'],
    ['id'=>42, 'vendedor_id'=>9,  'numero_whatsapp'=>'+5493516100009', 'tipo_consulta'=>'info_producto',   'mensaje_usuario'=>'¿Qué diferencia hay entre shoestring y straight cut?', 'respuesta_bot'=>'Shoestring: muy fino (3-4mm), crujiente, fast food. Straight cut: mediano (10-12mm), más carnoso, para restaurantes.', 'created_at'=>'2026-04-07 14:00:00'],
    ['id'=>43, 'vendedor_id'=>10, 'numero_whatsapp'=>'+5493516100010', 'tipo_consulta'=>'certificaciones', 'mensaje_usuario'=>'¿Tienen documentación técnica de los productos?',     'respuesta_bot'=>'Sí, McCain provee fichas técnicas completas. Solicitá al ejecutivo de cuenta o en el portal B2B.',             'created_at'=>'2026-04-07 16:00:00'],
    ['id'=>44, 'vendedor_id'=>2,  'numero_whatsapp'=>'+5511968951221', 'tipo_consulta'=>'pedidos',         'mensaje_usuario'=>'¿Cómo hago para ser distribuidor McCain?',             'respuesta_bot'=>'Para ser distribuidor autorizado, contactá la oficina comercial McCain de tu país con capacidad de almacenamiento a -18°C.',  'created_at'=>'2026-04-08 09:00:00'],
    ['id'=>45, 'vendedor_id'=>3,  'numero_whatsapp'=>'+5491144043309', 'tipo_consulta'=>'preparacion',     'mensaje_usuario'=>'¿Se pueden hacer al vapor las papas McCain?',          'respuesta_bot'=>'No es recomendado. Las papas McCain están diseñadas para fritura, horno o air fryer para lograr textura crujiente.',  'created_at'=>'2026-04-08 10:15:00'],
    ['id'=>46, 'vendedor_id'=>11, 'numero_whatsapp'=>'+5493414100011', 'tipo_consulta'=>'info_producto',   'mensaje_usuario'=>'¿Las papas son pre-fritas o crudas?',                  'respuesta_bot'=>'Pre-cocidas (blanqueadas o pre-fritas) antes del congelado IQF. Resultado: menos tiempo y menos aceite al cocinar.',  'created_at'=>'2026-04-08 14:30:00'],
    ['id'=>47, 'vendedor_id'=>12, 'numero_whatsapp'=>'+5493414100012', 'tipo_consulta'=>'ventajas',        'mensaje_usuario'=>'¿Qué argumentos uso para vender en hoteles?',          'respuesta_bot'=>'Para hoteles: consistencia en buffet, Hash Browns para desayuno, control de costos predecible, certs. Halal/Kosher.', 'created_at'=>'2026-04-08 15:45:00'],
    ['id'=>48, 'vendedor_id'=>5,  'numero_whatsapp'=>'+5491134100005', 'tipo_consulta'=>'saludo',          'mensaje_usuario'=>'Buenos días MAYA',                                    'respuesta_bot'=>'¡Buenos días, Matías! ¿En qué puedo ayudarte hoy con los productos McCain? 😊',                               'created_at'=>'2026-04-09 08:15:00'],
    ['id'=>49, 'vendedor_id'=>5,  'numero_whatsapp'=>'+5491134100005', 'tipo_consulta'=>'info_producto',   'mensaje_usuario'=>'¿Cuántos SKUs tiene McCain para food service?',        'respuesta_bot'=>'McCain ofrece más de 20 SKUs food service: múltiples cortes, wedges, hash browns, onion rings, mini roasters y más.', 'created_at'=>'2026-04-09 08:22:00'],
    ['id'=>50, 'vendedor_id'=>7,  'numero_whatsapp'=>'+5491134100007', 'tipo_consulta'=>'preparacion',     'mensaje_usuario'=>'¿Cómo logro papas más crujientes?',                   'respuesta_bot'=>'Tips: freidora a 175-180°C, no descongelar, no sobrecargar la canasta, escurrir bien y servir inmediatamente.',  'created_at'=>'2026-04-09 09:00:00'],
    ['id'=>51, 'vendedor_id'=>13, 'numero_whatsapp'=>'+5493414100013', 'tipo_consulta'=>'certificaciones', 'mensaje_usuario'=>'¿Tienen certificación ISO?',                          'respuesta_bot'=>'Sí, plantas de producción McCain certificadas con ISO 22000 (gestión de seguridad alimentaria).',                 'created_at'=>'2026-04-09 10:30:00'],
    ['id'=>52, 'vendedor_id'=>1,  'numero_whatsapp'=>'+573106512999',  'tipo_consulta'=>'registro',        'mensaje_usuario'=>'Quiero registrar a un nuevo vendedor de mi equipo',   'respuesta_bot'=>'Para registrar nuevos vendedores, pediles que escriban directo al número de WhatsApp de McCain. MAYA los guiará.',  'created_at'=>'2026-04-09 11:00:00'],
];

// ── Helpers ──────────────────────────────────────

function mcRequireLogin(): void {
    if (!isset($_SESSION['mccain_user'])) {
        header('Location: mccain_login.php');
        exit;
    }
}

function mcCurrentUser(): array {
    return $_SESSION['mccain_user'] ?? [];
}

function mcFormatDate(string $d): string {
    if (!$d) return '—';
    $dt = DateTime::createFromFormat('Y-m-d', $d)
       ?: DateTime::createFromFormat('Y-m-d H:i:s', $d);
    return $dt ? $dt->format('d/m/Y') : $d;
}

function mcFormatDateTime(string $d): string {
    if (!$d) return '—';
    $dt = DateTime::createFromFormat('Y-m-d H:i:s', $d);
    return $dt ? $dt->format('d/m/Y H:i') : $d;
}

function mcTipoLabel(string $tipo): string {
    $map = [
        'info_producto'   => '🥔 Info Producto',
        'ventajas'        => '⭐ Ventajas',
        'preparacion'     => '🍳 Preparación',
        'pedidos'         => '📦 Pedidos',
        'certificaciones' => '🏆 Certificaciones',
        'soporte'         => '🛠️ Soporte',
        'registro'        => '✅ Registro',
        'saludo'          => '👋 Saludo',
        'otro'            => '💬 Otro',
    ];
    return $map[$tipo] ?? '💬 ' . ucfirst($tipo);
}

function mcTipoColor(string $tipo): string {
    $map = [
        'info_producto'   => '#C8102E',
        'ventajas'        => '#e07b00',
        'preparacion'     => '#22c55e',
        'pedidos'         => '#3b82f6',
        'certificaciones' => '#8b5cf6',
        'soporte'         => '#f59e0b',
        'registro'        => '#06b6d4',
        'saludo'          => '#fbbf24',
        'otro'            => '#94a3b8',
    ];
    return $map[$tipo] ?? '#94a3b8';
}

function mcVendedorNombre(int $id): string {
    global $MCCAIN_VENDEDORES;
    return $MCCAIN_VENDEDORES[$id]['nombre'] ?? '—';
}
