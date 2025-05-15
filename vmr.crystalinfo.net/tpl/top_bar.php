<?php
include_once("init.php");

?>
<!DOCTYPE html>
<div id="top-bar">
    <div class="page-full-width cf">
        <ul id="nav" class="fl">
		
<li class="v-sep">
    <a href="#" class="round button dark ic-info image-left" id="help-button">
        <?php echo $lang['HELP']; ?>
    </a>
</li>

<!-- Bilgi Paneli -->
<div id="help-modal" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 50%; max-width: 500px; background: #2c3e50; color: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); z-index: 1000;">
    <h3 style="margin-top: 0;"><?php echo $lang['HELP_TITLE']; ?></h3>
    <p><?php echo $lang['HELP_TEXT']; ?></p>
    <button id="close-modal" style="background: #e74c3c; color: white; border: none; padding: 10px 15px; border-radius: 5px; cursor: pointer; float: right;">Kapat</button>
</div>

<!-- Arka Plan -->
<div id="modal-backdrop" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 999;"></div>

<script>
    // Modal Açma ve Kapama İşlemleri
    const helpButton = document.getElementById('help-button');
    const helpModal = document.getElementById('help-modal');
    const modalBackdrop = document.getElementById('modal-backdrop');
    const closeModal = document.getElementById('close-modal');

    helpButton.addEventListener('click', function(event) {
        event.preventDefault(); // Varsayılan davranışı engelle
        helpModal.style.display = 'block';
        modalBackdrop.style.display = 'block';
    });

    closeModal.addEventListener('click', function() {
        helpModal.style.display = 'none';
        modalBackdrop.style.display = 'none';
    });

    modalBackdrop.addEventListener('click', function() {
        helpModal.style.display = 'none';
        modalBackdrop.style.display = 'none';
    });
</script>

	
            <li class="v-sep">
                <a href="#" class="round button dark menu-definitions image-left">
                    <?php echo $lang['DEFINITIONS']; ?>
                </a>
                <ul>
                    <li><a href="user_details.php"><?php echo $lang['USERS']; ?></a></li>
                    <li><a href="view_personals.php"><?php echo $lang['PERSONALS']; ?></a></li>
                    <li><a href="view_customers.php"><?php echo $lang['CUSTOMERS']; ?></a></li>
                    <li><a href="view_supplier.php"><?php echo $lang['SUPPLIERS']; ?></a></li>
                </ul>
            </li>
            <li class="v-sep">
                <a href="#" class="round button dark menu-settings image-left">
                    <?php echo $lang['SETTINGS']; ?>
                </a>
                <ul>
                    <li><a href="update_details.php"><?php echo $lang['UPDATE_DEPOT']; ?></a></li>
                    <li><a href="webservice.php"><?php echo $lang['UPDATE_WEBSERVICE']; ?></a></li>
                </ul>
            </li>
			
				
            <li class="v-sep">
                <a href="#" class="round button dark menu-user image-left">
                    <strong><?php echo htmlspecialchars($DEPOSTOK['username'], ENT_QUOTES, 'UTF-8'); ?></strong>
                </a>
		
                <ul>
                    <li><a href="change_password.php"  class="round button dark"><?php echo $lang['CHANGE_PASSWORD']; ?></a></li>
	          </ul>
            </li>
		
			            <!-- Date and Time Display -->
            <form action="#" method="POST" id="search-form" class="fr" name="dateForm">
                <fieldset>
                    <div style="font-size:10px; color:white; display:flex; flex-direction:column; align-items:center; justify-content:center;">
                        <span id="timeDisplay"></span>
                        <span id="dateDisplay"></span>
                    </div>
                </fieldset>
            </form>
			
			
        </ul>
        
    <ul id="nav" class="fr">
    <li>
    <label class="switch">
        <input 
        type="checkbox" 
        id="languageToggle" 
        <?php echo ($_SESSION['lang'] === 'en') ? 'checked' : ''; ?>>
        <div class="slider round">
            <span class="lang-label tr">TR</span>
            <span class="lang-label en">EN</span>
        </div>
    </label>

    <script>
	document.getElementById('languageToggle').addEventListener('change', function () {
    const selectedLang = this.checked ? 'en' : 'tr';
    const currentUrl = new URL(window.location.href);

    // Eğer URL içinde lang parametresi varsa değiştir, yoksa ekle
    currentUrl.searchParams.set('lang', selectedLang);

    // Yeni URL'ye yönlendir
    window.location.href = currentUrl.toString();
});
</script>

        </li>
        <li><a href="logout.php" class="round button dark menu-logoff image-left"><?php echo $lang['LOGOFF']; ?></a></li>
    </ul>

    </div>
</div>

    <script>
    document.addEventListener("DOMContentLoaded", () => {
    const timeDisplay = document.getElementById("timeDisplay");
    const dateDisplay = document.getElementById("dateDisplay");

	const lang = "<?php echo $_SESSION['lang'] ?? 'tr'; ?>"; // PHP'den dil bilgisini al // Varsayılan 'tr'

    const months = {
        tr: ["Ocak", "Şubat", "Mart", "Nisan", "Mayıs", "Haziran", "Temmuz", "Ağustos", "Eylül", "Ekim", "Kasım", "Aralık"],
        en: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],
    };

    const days = {
        tr: ["Pazar", "Pazartesi", "Salı", "Çarşamba", "Perşembe", "Cuma", "Cumartesi"],
        en: ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"],
    };

    setInterval(() => {
        const now = new Date();
        const day = now.getDate();
        const month = months[lang][now.getMonth()];
        const year = now.getFullYear();
        const dayOfWeek = days[lang][now.getDay()];
        const time = now.toLocaleTimeString(lang === "tr" ? "tr-TR" : "en-US", { hour: "2-digit", minute: "2-digit", second: "2-digit" });

        if (timeDisplay) timeDisplay.textContent = time;
        if (dateDisplay) dateDisplay.textContent = `${day} ${month} ${year} ${dayOfWeek}`;
    }, 1000);
});
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>


