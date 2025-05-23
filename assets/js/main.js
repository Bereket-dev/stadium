const addTicket = document.querySelector(".add-ticket");
const removeTicket = document.querySelector(".remove-ticket");
const quantityArea = document.querySelector(".quantity-area");
const priceArea = document.querySelector(".price-area");

const totalPrice = document.getElementById("totalPrice");
const priceInput = document.getElementById("priceInput");
const quantityInput = document.getElementById("quantityInput");

let quantity = 0; //default value
let priceValue = 0; //default total price

addTicket.addEventListener("click", () => {
  quantity++;
  quantityArea.textContent = quantity;
  quantityInput.value = quantity;

  priceValue = priceArea.value * quantity;
  totalPrice.textContent = priceValue;
  priceInput.value = priceValue;
});

removeTicket.addEventListener("click", () => {
  if (quantity > 0) {
    quantity--;
  }
  quantityArea.textContent = quantity;
  quantityInput.value = quantity;

  priceValue = priceArea.value * quantity;
  totalPrice.textContent = priceValue;
  priceInput.value = priceValue;
});

//to give feature on header during scroll
window.addEventListener("scroll", function () {
  let header = document.querySelector("header nav");
  if (window.scrollY > 50) {
    header.classList.add("scrolled");
  } else {
    header.classList.remove("scrolled");
  }
});

function showHiddenForm(productId) {
  const formElement = document.getElementById("order-form-" + productId);
  const buyButton = document.querySelector(".order-add-" + productId);

  if (formElement.style.display == "none") {
    formElement.style.display = "block";
    buyButton.textContent = "cancel";
    buyButton.classList.remove("btn-primary");
    buyButton.classList.add("btn-secondary");
  } else if (formElement.style.display == "block") {
    formElement.style.display = "none";
    buyButton.textContent = "Order ->";
    buyButton.classList.add("btn-primary");
    buyButton.classList.remove("btn-secondary");
  }
}

function createSeatRow() {
  const row = document.createElement("div");
  row.classList.add("row", "mt-2");

  row.innerHTML = `                <div class="row">
                    <div class="col-md-3">
                        <label for="inputZip" class="form-label">Seat Type</label>
                        <input type="text" name="seat_type[]" placeholder="VIP" class="form-control" id="" required>
                    </div>
                    <div class="col-md-3">
                        <label for="inputZip" class="form-label">Amount</label>
                        <input type="number" name="seat_amount[]" placeholder="total seat" min="0" class="form-control" id="" required>
                    </div>
                    <div class="col-md-3">
                        <label for="inputZip" class="form-label">PRICE</label>
                        <input type="number" name="seat_price[]" placeholder="each price" min="0" class="form-control" id="" required>
                    </div>
                    <div class="addSeat col-md-1 btn-secondary btn align-self-end text-white " onclick = "addSeatRow()">+</div>
                    <div class="removeSeat col-md-1 btn btn-danger ms-1 btn align-self-end text-white " onclick = "removeSeatRow(this)">-</div>

                </div>`;

  return row;
}

function addSeatRow() {
  const container = document.querySelector(".seatContainer");
  const newRow = createSeatRow();

  container.appendChild(newRow);
}

function removeSeatRow(button) {
  const row = button.closest(".row");
  row.remove();
}
