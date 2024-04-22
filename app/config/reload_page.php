<!-- Skript, ktorý slúži na to, že pri reloade stránky ostaneme kde sme pre reloadom boli -->
<script>
    // Uložte aktuálnu pozíciu skrolovania do localStorage pred obnovením stránky
    window.onbeforeunload = function() {
        localStorage.setItem('scrollPosition', window.scrollY);
    };

    window.onload = function() {
        // Po načítaní stránky získajte pozíciu skrolovania z localStorage a posuňte stránku na túto pozíciu
        if (localStorage.getItem('scrollPosition') !== null) {
            window.scrollTo(0, localStorage.getItem('scrollPosition'));
            localStorage.removeItem('scrollPosition'); // Odstráňte hodnotu z localStorage
        }
    };
</script>