        </div>
    </div>
    
    <!-- Popup Notification Modal -->
    <div class="modal fade" id="popupNotification" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-0" id="popupHeader">
                    <h5 class="modal-title" id="popupTitle">
                        <i class="fas fa-info-circle me-2"></i>Notifikasi
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <div id="popupIcon" class="mb-3">
                        <i class="fas fa-info-circle fa-3x text-primary"></i>
                    </div>
                    <p class="mb-0 fs-5" id="popupMessage">Message here</p>
                </div>
                <div class="modal-footer border-0 justify-content-center">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sidebar toggle for mobile
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const sidebarToggle = document.getElementById('sidebarToggle');
        
        function toggleSidebar() {
            sidebar?.classList.toggle('show');
            sidebarOverlay?.classList.toggle('show');
        }
        
        function closeSidebar() {
            sidebar?.classList.remove('show');
            sidebarOverlay?.classList.remove('show');
        }
        
        // Toggle button click
        sidebarToggle?.addEventListener('click', function() {
            toggleSidebar();
        });
        
        // Click outside sidebar to close
        sidebarOverlay?.addEventListener('click', function() {
            closeSidebar();
        });
        
        // Close sidebar when clicking on a nav link (mobile)
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 768) {
                    closeSidebar();
                }
            });
        });
        
        // Popup Notification Function
        function showPopup(message, type = 'info') {
            const popup = document.getElementById('popupNotification');
            const popupHeader = document.getElementById('popupHeader');
            const popupTitle = document.getElementById('popupTitle');
            const popupIcon = document.getElementById('popupIcon');
            const popupMessage = document.getElementById('popupMessage');
            
            // Set message
            popupMessage.textContent = message;
            
            // Set styles based on type
            const configs = {
                success: {
                    headerClass: 'bg-success text-white',
                    icon: '<i class="fas fa-check-circle fa-3x text-success"></i>',
                    title: '<i class="fas fa-check-circle me-2"></i>Berhasil'
                },
                error: {
                    headerClass: 'bg-danger text-white',
                    icon: '<i class="fas fa-exclamation-circle fa-3x text-danger"></i>',
                    title: '<i class="fas fa-exclamation-circle me-2"></i>Error'
                },
                warning: {
                    headerClass: 'bg-warning text-dark',
                    icon: '<i class="fas fa-exclamation-triangle fa-3x text-warning"></i>',
                    title: '<i class="fas fa-exclamation-triangle me-2"></i>Peringatan'
                },
                info: {
                    headerClass: 'bg-primary text-white',
                    icon: '<i class="fas fa-info-circle fa-3x text-primary"></i>',
                    title: '<i class="fas fa-info-circle me-2"></i>Informasi'
                }
            };
            
            const config = configs[type] || configs.info;
            
            popupHeader.className = 'modal-header border-0 ' + config.headerClass;
            popupTitle.innerHTML = config.title;
            popupIcon.innerHTML = config.icon;
            
            // Show modal
            const modal = new bootstrap.Modal(popup);
            modal.show();
        }
    </script>
</body>
</html>
