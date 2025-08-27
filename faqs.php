<?php
include_once "assets/includes/doctype.php";
include_once "assets/includes/header.php";
// Note: Settings now loaded via doctype.php from database_settings.php
?>

<!-- Faq Section -->
<section id="faq" class="faq section light-background">
  <div class="container section-title" data-aos="fade-up">
    <h2>Frequently Asked Questions</h2>
    <p>Find answers to common questions about our services and platform.</p>
  </div>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-10" data-aos="fade-up" data-aos-delay="100">
        <div class="simple-faq-container">
          <?php
          // Show up to 10 FAQs, or fewer if less are set
          $max_faqs = min(10, count($faqs));
          for ($i = 0; $i < $max_faqs; $i++):
            $faq = $faqs[$i];
            ?>
            <div class="simple-faq-item" data-faq-index="<?php echo $i; ?>">
              <div class="simple-faq-question">
                <h3><?php echo htmlspecialchars($faq['question']); ?></h3>
                <span class="simple-faq-icon">+</span>
              </div>
              <div class="simple-faq-answer" style="display: <?php echo $i === 0 ? 'block' : 'none'; ?>;">
                <p><?php echo htmlspecialchars($faq['answer']); ?></p>
              </div>
            </div>
          <?php endfor; ?>
        </div>
      </div><!-- End Faq Column-->
    </div>
  </div>
</section><!-- /Faq Section -->

<style>
.simple-faq-container {
  max-width: 800px;
  margin: 0 auto;
}

.simple-faq-item {
  border: 1px solid #e0e0e0;
  border-radius: 8px;
  margin-bottom: 10px;
  background: white;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.simple-faq-question {
  padding: 20px;
  cursor: pointer;
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: var(--surface-color, #fff);
  border-radius: 8px;
  transition: background-color 0.3s ease;
}

.simple-faq-question:hover {
  background: var(--accent-color, #6c2eb6);
  color: white;
}

.simple-faq-question h3 {
  margin: 0;
  font-size: 18px;
  font-weight: 600;
  flex: 1;
  color: inherit;
}

.simple-faq-icon {
  font-size: 24px;
  font-weight: bold;
  color: inherit;
  transition: transform 0.3s ease;
  width: 30px;
  text-align: center;
}

.simple-faq-item.active .simple-faq-question {
  background: var(--accent-color, #6c2eb6);
  color: white;
}

.simple-faq-item.active .simple-faq-icon {
  transform: rotate(45deg);
}

.simple-faq-answer {
  padding: 0 20px 20px 20px;
  background: #f9f9f9;
  border-top: 1px solid #e0e0e0;
}

.simple-faq-answer p {
  margin: 0;
  line-height: 1.6;
  color: #333;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set first FAQ as active by default
    const firstFaq = document.querySelector('.simple-faq-item[data-faq-index="0"]');
    if (firstFaq) {
        firstFaq.classList.add('active');
    }
    
    // Add click handlers to all FAQ questions
    const faqQuestions = document.querySelectorAll('.simple-faq-question');
    
    faqQuestions.forEach(function(question) {
        question.addEventListener('click', function() {
            const faqItem = this.parentElement;
            const faqAnswer = faqItem.querySelector('.simple-faq-answer');
            const isActive = faqItem.classList.contains('active');
            
            // Close all other FAQs
            document.querySelectorAll('.simple-faq-item').forEach(function(item) {
                item.classList.remove('active');
                const answer = item.querySelector('.simple-faq-answer');
                if (answer) {
                    answer.style.display = 'none';
                }
            });
            
            // Toggle current FAQ
            if (!isActive) {
                faqItem.classList.add('active');
                if (faqAnswer) {
                    faqAnswer.style.display = 'block';
                }
            }
        });
    });
});
</script>

<?php
include_once "assets/includes/footer.php";
?>
