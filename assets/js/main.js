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

{
  /* <div class="seats-row col-12 row">
<div class="col-md-3">
    <label for="inputState" class="form-label">Seat Type</label>
    <select name="seat_type" id="inputState" class="form-select" required>
        <option selected>Choose...</option>
        <option value="vip">VIP</option>
        <option value="viip">VIIP</option>
        <option value="normal">NORMAL</option>
    </select>
</div>
<div class="col-md-3">
    <label for="inputZip" class="form-label">Amount</label>
    <input type="number" name="seat_amount" placeholder="total seat" min="0" class="form-control" id="" required>
</div>
<div class="col-md-3">
    <label for="inputZip" class="form-label">PRICE</label>
    <input type="number" name="seat_price" placeholder="each price" min="0" class="form-control" id="" required>
</div>
<div class="col-2 d-flex justify-content-start align-items-end">
    <button class=" text-white addSeat btn  " style="width: 50px; height: 40px; background-color: rgba(0,0,255, 0.4);">+</button>
</div>
</div> */
}
/// stadium registration

const addSeat = document.querySelector(".addSeat");
addSeat.addEventListener("click", () => {
  const myForm = e.closest("form");

  const seatRow = document.createElement("div");
  seatRow.classList.add("col-12 row");

  const firstCol = document.createElement("div");
  firstCol.classList.add("col-md-13");

  const firstColLabel = document.createElement("label");
  firstColLabel.classList.add("form-label");
  firstColLabel.htmlFor = "inpuSeat";
  firstColLabel.textContent = "Seat Type";

  const firstColSelect = document.createElement("select");
  firstColSelect.name = "seat_type";
  firstColSelect.classList.add("form-select");
  firstColSelect.required = true;

  const firstColOption1 = document.createElement("option");
  firstColOption1.selected = true;
  firstColOption1.textContent = "Choose ...";

  const firstColOption2 = document.createElement("option");
  firstColOption2.value = "Viip";
  firstColOption2.textContent = "Viip";

  const firstColOption3 = document.createElement("option");
  firstColOption3.value = "Vip";
  firstColOption3.textContent = "Vip";

  const firstColOption4 = document.createElement("option");
  firstColOption4.value = "Normal";
  firstColOption4.textContent = "Normal";

  const secondCol = document.createElement("div");
  secondCol.classList.add("col-md-13");

  const secondColLabel = document.createElement("label");
  secondColLabel.classList.add("form-label");
  secondColLabel.htmlFor = "inpuamount";
  secondColLabel.textContent = "Amount";

  const secondColInput = document.createElement("input");
  secondColInput.type = "number";
  secondColInput.name = "seat_amount";
  secondColInput.classList.add("form-control");
  secondColInput.placeholder = "total amount";
  secondColInput.min = 0;

  const thirdCol = document.createElement("div");
  thirdCol.classList.add("col-md-13");
  const thirdColLabel = document.createElement("label");
  thirdColLabel.classList.add("form-label");
  thirdColLabel.htmlFor = "inpuprice";
  thirdColLabel.textContent = "Price";

  const thirdColInput = document.createElement("input");
  thirdColInput.type = "number";
  thirdColInput.name = "seat_price";
  thirdColInput.classList.add("form-control");
  thirdColInput.placeholder = "price";
  thirdColInput.min = 0;

  seatRow.appendChild(firstCol);
  firstCol.appendChild(firstColLabel);
  firstCol.appendChild(firstColSelect);
  firstColSelect.appendChild(firstColOption1);
  firstColSelect.appendChild(firstColOption2);
  firstColSelect.appendChild(firstColOption3);
  firstColSelect.appendChild(firstColOption4);

  seatRow.appendChild(secondCol);
  secondCol.appendChild(secondColLabel);
  secondCol.appendChild(secondColInput);

  seatRow.appendChild(thirdCol);
  thirdCol.appendChild(thirdColLabel);
  thirdCol.appendChild(thirdColInput);

  myForm.appendChild(seatRow);
});
