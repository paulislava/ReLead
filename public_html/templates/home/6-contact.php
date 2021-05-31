<section id="contact" class="fullscreen text-center fl-animation fl-slide-in-up">
    <main class="padding1">
    <h1>Задайте вопрос</h1>
    <form id="contactForm" action="<?=HOME_URI?>/ajax/contact.php" method="POST">
        <div class="row">
         <div class="col">
          <input type="text" name="name" class="form-control" placeholder="Имя" required>
        </div>
        <div class="col">
          <input type="tel" name="phone" class="form-control" placeholder="Телефон">
        </div>
        </div>
        <input type="email" name="email" class="form-control" placeholder="E-mail" required>
        <textarea name="message" class="form-control" placeholder="Ваше сообщение" required></textarea>
        <button type="submit" class="btn btn2 submitBtn">Отправить</button>
    </form>
    </main>
</section>