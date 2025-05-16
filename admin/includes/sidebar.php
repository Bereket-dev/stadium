<div class="side_bar position-fixed bg-primary text-white ps-4 py-5">
  <div class="myacount d-flex align-items-center gap-2">
    <div class="admin_image position-relative bg-secondary">
      <img src="" alt="" class="postion-absolut img-fluid" />
    </div>
    <div class="dropdown">
      <a
        class="dropdown-toggle text-decoration-none text-white"
        href="#"
        role="button"
        id="dropdownMenuLink"
        data-bs-toggle="dropdown"
        aria-expanded="false">
        <?php
        $stmt = $conn->prepare("SELECT * FROM users WHERE id = ? ");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();

        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $username = $row["first_name"];
        $stmt->close();

        echo $username; ?>
      </a>

      <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
        <li>
          <a
            href="../auth/logout.php"
            class="dropdown-item btn btn-outline-primary">logout</a>
        </li>
      </ul>
    </div>
  </div>

  <div class="mt-5 d-flex flex-column gap-3">
    <a
      href="./admin_dashboard.php"
      class="border-bottom text-decoration-none text-white">Dashboard</a>
    <a
      href="./book-management.php"
      class="border-bottom text-decoration-none text-white">Booking Management</a>
    <a
      href="./event-management.php"
      class="border-bottom text-decoration-none text-white">Event Management</a>
    <a href="./product-management.php" class="border-bottom text-decoration-none text-white">Food & Drinks</a>
    <a href="./stadium.register.php" class="text-decoration-none text-white">Stadium Registration</a>
  </div>
</div>