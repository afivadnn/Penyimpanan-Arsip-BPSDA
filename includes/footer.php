<!-- footer.php -->
    </div> <!-- End main content wrapper -->

    <!-- Global Scripts -->
    <script>
        // Sidebar toggle functions (centralized control)
        function closeSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');
            
            if (sidebar) sidebar.classList.add('-translate-x-full');
            if (overlay) {
                overlay.classList.add('opacity-0', 'pointer-events-none');
            }
        }

        function openSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');
            
            if (sidebar) sidebar.classList.remove('-translate-x-full');
            if (overlay) {
                overlay.classList.remove('opacity-0', 'pointer-events-none');
            }
        }

        // Mobile menu toggle button handler
        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.getElementById('menu-toggle');
            const overlay = document.getElementById('overlay');

            if (menuToggle) {
                menuToggle.addEventListener('click', openSidebar);
            }

            if (overlay) {
                overlay.addEventListener('click', closeSidebar);
            }

            // ESC key to close sidebar on mobile
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && window.innerWidth < 768) {
                    closeSidebar();
                }
            });

            // Close sidebar when clicking internal links on mobile
            if (window.innerWidth < 768) {
                const sidebarLinks = document.querySelectorAll('#sidebar a');
                sidebarLinks.forEach(link => {
                    link.addEventListener('click', () => {
                        setTimeout(closeSidebar, 150);
                    });
                });
            }
        });

        // Handle resize - ensure sidebar state is correct
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                if (window.innerWidth >= 768) {
                    // Desktop: ensure sidebar is visible and overlay hidden
                    const sidebar = document.getElementById('sidebar');
                    const overlay = document.getElementById('overlay');
                    if (sidebar) sidebar.classList.remove('-translate-x-full');
                    if (overlay) overlay.classList.add('opacity-0', 'pointer-events-none');
                }
            }, 250);
        });

        // Optional: Add loading state handler for smooth navigation
        window.addEventListener('beforeunload', function() {
            document.body.style.opacity = '0.7';
        });
    </script>

    

</body>
</html>