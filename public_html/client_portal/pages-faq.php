<?php
require_once dirname(__DIR__, 2) . '/private/gws-universal-config.php';
include_once 'assets/includes/auth_check.php';
include_once 'assets/includes/doctype.php';
include_once 'assets/includes/header.php';
include_once 'assets/includes/sidebar.php';
include_once 'assets/includes/breadcrumbs.php';
?>

    <section class="section faq">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card">
                    <div class="card-header pb-0">
                        <ul class="nav nav-tabs card-header-tabs" id="faqTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab" aria-controls="general" aria-selected="true">General</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="blog-tab" data-bs-toggle="tab" data-bs-target="#blog" type="button" role="tab" aria-controls="blog" aria-selected="false">Blog</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="portal-tab" data-bs-toggle="tab" data-bs-target="#portal" type="button" role="tab" aria-controls="portal" aria-selected="false">Portal</button>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="faqTabContent">
                            <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                                <div class="accordion accordion-flush" id="faq-group-general">
                                    <!-- 10 General FAQ items -->
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" data-bs-target="#faqsGeneral-1" type="button" data-bs-toggle="collapse">
                                                Where are the General Questions?
                                            </button>
                                        </h2>
                                        <div id="faqsGeneral-1" class="accordion-collapse collapse" data-bs-parent="#faq-group-general">
                                            <div class="accordion-body">
                                                When generally relevant questions are submitted to the Administrator, you will see them answered here.
                                            </div>
                                        </div>
                                    </div>
                                    <?php for ($i = 2; $i <= 10; $i++): ?>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" data-bs-target="#faqsGeneral-<?= $i ?>" type="button" data-bs-toggle="collapse">
                                                General Question <?= $i ?>?
                                            </button>
                                        </h2>
                                        <div id="faqsGeneral-<?= $i ?>" class="accordion-collapse collapse" data-bs-parent="#faq-group-general">
                                            <div class="accordion-body">
                                                This is the answer to General Question <?= $i ?>. Replace with real content.
                                            </div>
                                        </div>
                                    </div>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="blog" role="tabpanel" aria-labelledby="blog-tab">
                                <div class="accordion accordion-flush" id="faq-group-blog">
                                <!-- 10 Blog FAQ items -->
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" data-bs-target="#faqsBlog-1" type="button" data-bs-toggle="collapse">
                                            What are the requirements to comment on a blog post?
                                        </button>
                                    </h2>
                                    <div id="faqsBlog-1" class="accordion-collapse collapse" data-bs-parent="#faq-group-blog">
                                        <div class="accordion-body">
                                            People who are registered in our database can log onto this portal and leave a comment.  It is up to the Administrator to decide if guests can comment, but for security reasons, we default to not allowing guests to comment on posts.  It is possible to register as a blog user, where you can comment on the blog, view/edit your comments and set up a blog user profile, without being a full registered member.
                                        </div>
                                    </div>
                                </div>
                                <?php for ($i = 2; $i <= 10; $i++): ?>
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" data-bs-target="#faqsBlog-<?= $i ?>" type="button" data-bs-toggle="collapse">
                                            Blog Question <?= $i ?>?
                                        </button>
                                    </h2>
                                    <div id="faqsBlog-<?= $i ?>" class="accordion-collapse collapse" data-bs-parent="#faq-group-blog">
                                        <div class="accordion-body">
                                            This is the answer to Blog Question <?= $i ?>. Replace with real content.
                                        </div>
                                    </div>
                                </div>
                                <?php endfor; ?>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="portal" role="tabpanel" aria-labelledby="portal-tab">
                                <div class="accordion accordion-flush" id="faq-group-portal">
                                    <!-- 10 Portal FAQ items -->
                                    <?php for ($i = 1; $i <= 10; $i++): ?>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" data-bs-target="#faqsPortal-<?= $i ?>" type="button" data-bs-toggle="collapse">
                                                Portal Question <?= $i ?>?
                                            </button>
                                        </h2>
                                        <div id="faqsPortal-<?= $i ?>" class="accordion-collapse collapse" data-bs-parent="#faq-group-portal">
                                            <div class="accordion-body">
                                                This is the answer to Portal Question <?= $i ?>. Replace with real content.
                                            </div>
                                        </div>
                                    </div>
                                    <?php endfor; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php include_once 'assets/includes/footer.php'; ?>