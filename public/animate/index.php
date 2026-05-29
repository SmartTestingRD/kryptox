<?php 
  
  $base = '../';
  include $base.'flexr/ini.php';


?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>KryptoX Animation</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
      html{
        overflow: hidden;
      }
      body {
        overflow: hidden;
        font-family: 'Plus Jakarta Sans', sans-serif;
        margin: 0;
        padding: 0;
        overflow-x: hidden;
      }
      .glass {
        background: rgba(255, 255, 255, 0.03);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
      }
      .dark .glass {
        background: rgba(0, 0, 0, 0.2);
      }
      @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-5px); }
      }
      .float-animation {
        animation: float 3s ease-in-out infinite;
      }
    </style>

    <script>
      // (async function () {
      //   const FILES = [
      //     {
      //       key: 'kryptox_tokens_v3.4',
      //       url: '/animate/motion.json'
      //     },
      //     {
      //       key: 'kryptox_layout_v3.4',
      //       url: '/animate/layout.json'
      //     }
      //   ];

      //   var s = '<?php echo $raiz; ?>';

      //   for (const file of FILES) {
      //     try {
      //       const response = await fetch(s+file.url, { cache: 'no-store' });
      //       if (!response.ok) throw new Error(`Failed to load ${file.url}`);

      //       const json = await response.json();

      //       localStorage.setItem(file.key, JSON.stringify(json));
      //       console.log(`✔ Stored ${file.key} in localStorage`);
      //     } catch (err) {
      //       console.error(`✖ Error loading ${file.key}`, err);
      //     }
      //   }
      // })();
  </script>

    <!-- <link rel="stylesheet" href="./index.css"> -->
      <script type="importmap">
    {
      "imports": {
        "react-dom/": "https://esm.sh/react-dom@^19.2.3/",
        "react/": "https://esm.sh/react@^19.2.3/",
        "react": "https://esm.sh/react@^19.2.3",
        "lucide-react": "https://esm.sh/lucide-react@^0.562.0"
      }
    }
    </script>
    <!-- <script type="module" crossorigin src="assets/index-BF4lAJfr.js"></script> -->



  <script type="module">
  const KEYS = {
    layout: "kryptox_layout_v3.4",
    tokens: "kryptox_tokens_v3.4",
    cursor: "kryptox_cursor_v3.4",
    meta:  "kryptox_boot_meta_v3.4"
  };

  const sleep = (ms) => new Promise(r => setTimeout(r, ms));

  function resolveRoot() {
    const u = new URL(window.location.href);
    if (!u.pathname.endsWith("/")) u.pathname = u.pathname.replace(/\/[^\/]*$/, "/");
    return u;
  }

  async function fetchJSON(urlObj) {
    const u = new URL(urlObj.toString());
    u.searchParams.set("v", String(Date.now())); // cache-bust fuerte
    const res = await fetch(u.toString(), { cache: "no-store" });
    if (!res.ok) throw new Error(`No se pudo cargar: ${u.pathname}`);
    return await res.json();
  }

  async function preloadFromFiles_FORCE() {
    const root = resolveRoot();

    const layoutURL = new URL("./layout.json", root);
    const motionURL = new URL("./motion.json", root);

    const [layout, motion] = await Promise.all([
      fetchJSON(layoutURL),
      fetchJSON(motionURL)
    ]);

    // Validación layout mínima
    if (!layout || typeof layout !== "object") throw new Error("layout.json inválido");
    if (!Array.isArray(layout.wallets) || !Array.isArray(layout.modules) || !Array.isArray(layout.lines)) {
      throw new Error("layout.json debe tener { wallets:[], modules:[], lines:[] }");
    }

    // motion.json en tu caso es un ARRAY con tokens + cursor al final
    if (!Array.isArray(motion)) throw new Error("motion.json debe ser un array");

    const cursorItem = motion.find(x => x && x.id === "mouse-cursor" && x.type === "cursor");
    if (!cursorItem) throw new Error("motion.json no contiene {id:'mouse-cursor', type:'cursor'}");

    const tokensOnly = motion.filter(x => !(x && x.id === "mouse-cursor" && x.type === "cursor"));

    // 🔥 Fuerza escritura SIEMPRE (esto evita el 'skip write' que te está matando)
    localStorage.setItem(KEYS.layout, JSON.stringify(layout));
    localStorage.setItem(KEYS.tokens, JSON.stringify(tokensOnly));
    localStorage.setItem(KEYS.cursor, JSON.stringify(cursorItem));
    localStorage.setItem(KEYS.meta, JSON.stringify({ source: "files", loadedAt: new Date().toISOString() }));

    // console.log("✅ FORCE preload OK (layout/tokens/cursor) desde archivos");
    // console.log("layout:", layout);
    // console.log("tokens:", tokensOnly.length, "cursor:", cursorItem);
  }

  try {
    await preloadFromFiles_FORCE();
  } catch (e) {
    console.warn("⚠️ Precarga falló, el bundle usará defaults:", e);
  }

  // pequeño delay para asegurar que el storage esté listo antes del render
  await sleep(30);

  // ✅ IMPORT DINÁMICO con await real
  await import("./assets/index-BF4lAJfr.js");
</script>



</head>
  <body>
    <div id="root"></div>
</body>
</html>
