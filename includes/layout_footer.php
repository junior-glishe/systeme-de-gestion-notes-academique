    </main>
    </div>
    </div>
    <script>
      const sidebar = document.getElementById('sidebar');
      const main = document.getElementById('mainArea');
      new MutationObserver(() => {
        main.classList.toggle('ml-20', sidebar.classList.contains('sidebar-collapsed'));
        main.classList.toggle('ml-64', !sidebar.classList.contains('sidebar-collapsed'));
      }).observe(sidebar, {
        attributes: true,
        attributeFilter: ['class']
      });
    </script>
    </body>

    </html>