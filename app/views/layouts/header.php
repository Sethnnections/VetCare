 <div class="header-main">
            <div class="header-left">
                <button class="toggle-button" id="sidebarToggle">
                    <span class="btn-icon-wrap">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                </button>
                <button class="mobile-nav-toggle d-md-none" id="mobileSidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            
            <div class="header-search">
                <div class="search-input-group">
                    <i class="fas fa-search"></i>
                    <input type="text" class="form-control" placeholder="Find Something...">
                </div>
            </div>
            
            <div class="header-right">
                <div class="user-profile dropdown">
                    <div class="user-info">
                        <h6 class="user-name">
                            <?php 
                            if(!empty($_SESSION['first_name'])) {
                                echo htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']);
                            } else {
                                echo htmlspecialchars($_SESSION['username']);
                            }
                            ?>
                        </h6>
                        <div class="user-role"><?php echo ucfirst($_SESSION['role']); ?></div>
                    </div>
                    <div class="user-avatar">
                        <?php 
                        $initials = '';
                        if(!empty($_SESSION['first_name'])) {
                            $initials = strtoupper(substr($_SESSION['first_name'], 0, 1));
                            if(!empty($_SESSION['last_name'])) {
                                $initials .= strtoupper(substr($_SESSION['last_name'], 0, 1));
                            }
                        } else {
                            $initials = strtoupper(substr($_SESSION['username'], 0, 2));
                        }
                        echo $initials;
                        ?>
                    </div>
                </div>
            </div>
        </div>