</div> <!-- End main-content if opened in header -->

    <!-- Footer -->
    <footer class="footer mt-auto py-3 bg-light border-top">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start">
                    <span class="text-muted">
                        &copy; <?php echo date('Y'); ?> <strong><?php echo APP_NAME; ?></strong> - Version <?php echo APP_VERSION; ?>
                    </span>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <span class="text-muted">
                        Developed with <i class="bi bi-heart-fill text-danger"></i> by Your Team
                    </span>
                </div>
            </div>
        </div>
    </footer>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    
    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Main JS -->
    <script src="<?php echo BASE_URL; ?>/assets/js/main.js"></script>
    
    <?php if (isset($includeCalculator) && $includeCalculator): ?>
    <!-- Calculator JS (untuk halaman kasir) -->
    <script src="<?php echo BASE_URL; ?>/assets/js/calculator.js"></script>
    <?php endif; ?>
    
    <!-- Custom page scripts -->
    <?php if (isset($pageScript)): ?>
        <script src="<?php echo BASE_URL . $pageScript; ?>"></script>
    <?php endif; ?>
    
    <!-- Inline scripts -->
    <?php if (isset($inlineScript)): ?>
        <script><?php echo $inlineScript; ?></script>
    <?php endif; ?>

</body>
</html>
