<link href="https://cdn.jsdelivr.net/npm/remixicon@3.2.0/fonts/remixicon.css" rel="stylesheet">


<header class="header">
    <!-- <nav class="nav__container"> -->
    <nav class="nav__container flex-no" >
        <div class="nav__data">
            <a href="/" class="nav__logo">
                <img class="nav__logo-pic" src="<?= ROOT_DIR ?>/assets/images/logo.png" alt="logo">
            </a>

            
        </div>

        <!--=============== NAV MENU ===============-->
        <!-- <div class="nav__menu" id="nav-menu"> -->
   
    </nav>
</header>

<script>
    const showMenu = (toggleId, navId) =>{
    const toggle = document.getElementById(toggleId),
          nav = document.getElementById(navId)
 
    toggle.addEventListener('click', () =>{
        // Add show-menu class to nav menu
        nav.classList.toggle('show-menu')
 
        // Add show-icon to show and hide the menu icon
        toggle.classList.toggle('show-icon')
    })
 }
 
 showMenu('nav-toggle','nav-menu')
</script>