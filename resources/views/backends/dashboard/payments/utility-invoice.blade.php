@extends('backends.layouts.app')


@push('style')
    {{-- {{ asset('assets') }}/css/ --}}
    <link href="{{ asset('assets') }}/css/flatpickr.min.css" rel="stylesheet" type="text/css">
@endpush



@section('content')
    <div class="container-xxl">


        <div class="page-title-head d-flex align-items-sm-center flex-sm-row flex-column gap-2">
            <div class="flex-grow-1">
                <h4 class="fs-18 text-uppercase fw-bold mb-0">Create Invoice</h4>
            </div>

            <div class="text-end">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Boron</a></li>

                    <li class="breadcrumb-item"><a href="javascript: void(0);">Invoices</a></li>

                    <li class="breadcrumb-item active">Create Invoice</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card position-relative">
                    <form>
                        <div class="card-body">
                            <!-- Invoice Logo-->
                            <div class="d-flex align-items-start justify-content-between mb-4">
                                <div class="overflow-hidden position-relative border rounded d-flex align-items-center justify-content-start px-2"
                                    style="height: 60px; width: 260px;">
                                    <label for="imageInput" class="position-absolute top-0 start-0 end-0 bottom-0"></label>
                                    @if (session('locale') == 'kh')
                                        <img src="{{ asset('assets/images/logo-dark(kh).png') }}" alt="Preview Image"
                                            height="55">
                                    @else
                                        <span class="logo-lg"><img src="{{ asset('assets') }}/images/logo-dark.png"
                                                alt="Preview Image" height="55"></span>
                                    @endif
                                </div>

                                <div class="text-end">
                                    <div class="row g-1 align-items-center">
                                        <div class="col-auto">
                                            <label for="invoiceNo" class="col-form-label fs-16 fw-bold">#INV</label>
                                        </div>
                                        <div class="col-auto">
                                            <input type="number" id="invoiceNo" class="form-control" placeholder="00001234">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">

                                {{-- LEFT COLUMN: Contract & Room Information --}}
                                <div class="col-md-6">
                                    {{-- Contract Selection --}}
                                    <div class="mb-3">
                                        <label for="contract_id" class="form-label">Select Contract (Room -
                                            Tenant)</label>
                                        <select class="form-select" id="contract_id" name="contract_id" required>
                                            <option></option> {{-- Blank for placeholder --}}
                                            <option value="1" data-room-number="101" data-room-price="250.00">Room 101
                                                -
                                                John Doe</option>
                                            <option value="2" data-room-number="102" data-room-price="275.00">Room 102
                                                -
                                                Jane Smith</option>
                                        </select>
                                    </div>

                                    {{-- Display fields for Room Info --}}
                                    <div class="row">
                                        <div class="col-lg-6 mb-3">
                                            <label for="room_number" class="form-label">Room Number</label>
                                            <input type="text" id="room_number" class="form-control" readonly>
                                        </div>
                                        <div class="col-lg-6 mb-3">
                                            <label for="room_price" class="form-label">Room Price</label>
                                            <div class="input-group">
                                                <span class="input-group-text">{{ auth()->user()->currency_code == 'KHR' ? '៛' : '$' }}</span>
                                                <input type="text" id="room_price" class="form-control" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- RIGHT COLUMN: Invoice & Payment Details --}}
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-lg-6 mb-3">
                                            <label for="issue_date" class="form-label">Invoice Date</label>
                                            <input type="date" class="form-control" id="issue_date" name="issue_date"
                                                value="{{ date('Y-m-d') }}">
                                        </div>
                                        <div class="col-lg-6 mb-3">
                                            <label for="due_date" class="form-label">Due Date</label>
                                            <input type="date" class="form-control" id="due_date" name="due_date">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-6 mb-3">
                                            <label for="payment_status" class="form-label">Payment Status</label>
                                            <select class="form-select" id="payment_status" name="status">
                                                <option value="unpaid" selected>Unpaid</option>
                                                <option value="paid">Paid</option>
                                                <option value="overdue">Overdue</option>
                                                <option value="void">Void</option>
                                            </select>
                                        </div>
                                        <div class="col-lg-6 mb-3">
                                            <label for="payment_method" class="form-label">Payment Method</label>
                                            <select class="form-select" id="payment_method" name="payment_method">
                                                <option value="" selected>N/A</option>
                                                <option value="cash">Cash</option>
                                                <option value="aba_pay">ABA Pay</option>
                                                <option value="wing">Wing</option>
                                                <option value="bank_transfer">Bank Transfer</option>
                                            </select>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="mt-4">
                                <div class="table-responsive">
                                    <table class="table text-center table-nowrap align-middle mb-0">
                                        <thead>
                                            <tr class="bg-light bg-opacity-50">
                                                <th scope="col" class="border-0" style="width: 70px;">#</th>
                                                <th scope="col" class="border-0 text-start">Utility Details</th>
                                                <th scope="col" class="border-0" style="width: 140px">Quantity</th>
                                                <th scope="col" class="border-0" style="width: 140px;">Unit price</th>
                                                <th scope="col" class="border-0" style="width: 240px">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <th scope="row">01</th>
                                                <td class="text-start">
                                                    <input type="text" id="utility-detail-one" class="form-control mb-1"
                                                        placeholder="Utility One">
                                                </td>
                                                <td>
                                                    <input type="number" id="utility-category-one" class="form-control"
                                                        placeholder="Quantity">
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control" placeholder="Price">
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control w-auto" placeholder="{{ auth()->user()->currency_code == 'KHR' ? '0៛' : '$0.00' }}">
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">02</th>
                                                <td class="text-start">
                                                    <input type="text" id="utility-detail-two" class="form-control mb-1"
                                                        placeholder="Utility Two">
                                                </td>
                                                <td>
                                                    <input type="number" id="utility-category-two" class="form-control"
                                                        placeholder="Quantity">
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control" placeholder="Price">
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control w-auto" placeholder="{{ auth()->user()->currency_code == 'KHR' ? '0៛' : '$0.00' }}">
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div>
                                    <table class="table table-sm table-borderless table-nowrap align-middle mb-0 ms-auto"
                                        style="width:300px">
                                        <tbody>
                                            <tr>
                                                <td class="fw-medium">Subtotal</td>
                                                <td class="text-end">
                                                    <div class="ms-auto" style="width: 160px;">
                                                        <input type="number" id="productSubtotal" class="form-control"
                                                            placeholder="{{ auth()->user()->currency_code == 'KHR' ? '0៛' : '$0.00' }}">
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="fw-medium">Discount <small class="text-muted">(10%)</small>
                                                </td>
                                                <td class="text-end">
                                                    <div class="ms-auto" style="width: 160px;">
                                                        <input type="number" id="productDiscount" class="form-control"
                                                            placeholder="{{ auth()->user()->currency_code == 'KHR' ? '0៛' : '$0.00' }}">
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="fw-medium">Tax <small class="text-muted">(18%)</small></td>
                                                <td class="text-end">
                                                    <div class="ms-auto" style="width: 160px;">
                                                        <input type="number" id="productTaxes" class="form-control"
                                                            placeholder="{{ auth()->user()->currency_code == 'KHR' ? '0៛' : '$0.00' }}">
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr class="fs-15">
                                                <th scope="row" class="fw-bold">Total Amount</th>
                                                <th class="text-end">
                                                    <div class="ms-auto" style="width: 160px;">
                                                        <input type="number" id="productTotalAmount" disabled=""
                                                            class="form-control" placeholder="{{ auth()->user()->currency_code == 'KHR' ? '0៛' : '$0.00' }}">
                                                    </div>
                                                </th>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div>
                                <label class="form-label" for="InvoiceNote"> Note : </label>
                                <textarea class="form-control" id="InvoiceNote" placeholder="Thanks for your business "
                                    rows="3"></textarea>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="mb-5">
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" id="preview-invoice-btn" class="btn btn-primary gap-1">
                            <i class="ti ti-eye fs-16"></i> Preview
                        </button>
                        <a href="javascript: void(0);" class="btn btn-success gap-1"><i
                                class="ti ti-device-floppy fs-16"></i> Save</a>
                        <a href="javascript: void(0);" class="btn btn-info gap-1"><i class="ti ti-send fs-16"></i> Send
                            Invoice</a>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection



@push('script')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const contractSelect = document.getElementById('contract_id');
            const roomNumberInput = document.getElementById('room_number');
            const roomPriceInput = document.getElementById('room_price');

            contractSelect.addEventListener('change', function () {
                const selectedOption = this.options[this.selectedIndex];

                const roomNumber = selectedOption.dataset.roomNumber || '';
                const roomPrice = selectedOption.dataset.roomPrice || '';

                roomNumberInput.value = roomNumber;
                roomPriceInput.value = parseFloat(roomPrice).toFixed(2);
            });
        });
    </script>

    <script>
        const logoUrl = "{{ asset('assets/images/logo-dark.png') }}";
        const qrCodeUrl = "{{ asset('assets/images/qr-code.png') }}";
        const signatureUrl = "{{ asset('assets/images/signature.png') }}";
        const qrCode1Url = "{{ asset('assets/images/qr1.jpg') }}";
        const qrCode2Url = "{{ asset('assets/images/qr2.jpg') }}";

        document.addEventListener('DOMContentLoaded', function () {

            const contractSelect = document.getElementById('contract_id');
            if (contractSelect) {
                const roomNumberInput = document.getElementById('room_number');
                const roomPriceInput = document.getElementById('room_price');
                contractSelect.addEventListener('change', function () {
                    const selectedOption = this.options[this.selectedIndex];
                    const roomNumber = selectedOption.dataset.roomNumber || '';
                    const roomPrice = selectedOption.dataset.roomPrice || '';
                    roomNumberInput.value = roomNumber;
                    roomPriceInput.value = parseFloat(roomPrice).toFixed(2);
                });
            }

            const previewButton = document.getElementById('preview-invoice-btn');
            if (previewButton) {
                previewButton.addEventListener('click', function () {

                    const invoiceNumber = document.getElementById('invoiceNo').value || '7896';
                    const contractSelect = document.getElementById('contract_id');
                    const contractText = contractSelect.options[contractSelect.selectedIndex].text;
                    const roomNumber = document.getElementById('room_number').value;
                    const roomPrice = parseFloat(document.getElementById('room_price').value) || 0;
                    const issueDate = document.getElementById('issue_date').value;
                    const dueDate = document.getElementById('due_date').value;
                    const status = document.getElementById('payment_status').value;
                    const note = document.getElementById('InvoiceNote').value;
                    
                    // Get the currency symbol from user preferences
                    const currencySymbol = '{{ auth()->user()->currency_code == "KHR" ? "៛" : "$" }}';
                    const currencyFormat = (amount) => {
                        return '{{ auth()->user()->currency_code == "KHR" ? "" : "$" }}' + amount.toFixed(2) + '{{ auth()->user()->currency_code == "KHR" ? "៛" : "" }}';
                    };

                    let statusClass = 'bg-warning-subtle text-warning';
                    if (status === 'paid') {
                        statusClass = 'bg-success-subtle text-success';
                    } else if (status === 'void') {
                        statusClass = 'bg-danger-subtle text-danger';
                    }

                    const subtotal = roomPrice;
                    const totalAmount = roomPrice;

                    const invoiceHtml = `
                                <!DOCTYPE html>
                                <html lang="en">
                                <head>
                                    <meta charset="UTF-8">
                                    <title>Invoice #${invoiceNumber}</title>
                                    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
                                    <style>
                                        body { background-color: #fff !important; font-family: sans-serif; font-size: 14px; }
                                        .invoice-card { box-shadow: none; border: none; }
                                        .info-label { font-weight: 600; font-size: 14px; margin-bottom: 0.5rem; }
                                        .info-text { font-size: 14px; color: #6c757d; line-height: 1.6; }
                                        .invoice-items-table thead { background-color: #f8f9fa; }

                                        /* --- MODIFIED CSS FOR TOTALS SECTION --- */

                                        /* Remove all borders from total rows by default */
                                        .invoice-items-table .totals-row td,
                                        .invoice-items-table .final-total-row td {
                                            border: none;
                                            padding-top: 0.75rem;
                                            padding-bottom: 0.75rem;
                                        }

                                        /* Apply thin top border to the last two cells of the subtotal row */
                                        .invoice-items-table .totals-row td:nth-last-child(-n+2) {
                                            border-top: 1px solid #e9ecef;
                                        }

                                        /* Apply thick top/bottom borders to the last two cells of the final total row */
                                        .invoice-items-table .final-total-row td:nth-last-child(-n+2) {
                                            border-top: 2px solid #212529;
                                            border-bottom: 2px solid #212529;
                                        }

                                        /* Ensure all text in the final total row is bold */
                                        .invoice-items-table .final-total-row td {
                                            font-weight: 700;
                                        }
                                    </style>
                                </head>
                                <body>
                                    <div class="card invoice-card">
                                        <div class="card-body">
                                            <div class="row align-items-center mb-5">
                                                <div class="col-6">
                                                    <img src="${logoUrl}" alt="logo" height="65">
                                                </div>
                                                <div class="col-6 text-end">
                                                    <h5 class="m-0 fw-bolder fs-20">Invoice: #INV${invoiceNumber}</h5>
                                                    <span class="badge ${statusClass} px-2 fs-12 mt-2 py-1">${status.toUpperCase()}</span>
                                                </div>
                                            </div>

                                            <div class="row mb-4">
                                                <div class="col-4">
                                                    <h5 class="info-label">Invoice From:</h5>
                                                    <p class="info-text mb-0">
                                                        <strong>RoomGate Inc.</strong><br>
                                                        123 Property Lane,<br>
                                                        Phnom Penh, 12000<br>
                                                        Phone: (855) 12-345-678
                                                    </p>
                                                </div>
                                                <div class="col-4">
                                                    <h5 class="info-label">Bill To:</h5>
                                                    <p class="info-text mb-0">
                                                        <strong>Pheakdey</strong><br>
                                                        Room ${roomNumber}<br>
                                                        Phnom Penh, 12000
                                                    </p>
                                                </div>
                                                <div class="col-4 text-end">
                                                    <div class="d-flex justify-content-end gap-2">
                                                        <img src="${qrCode1Url}" alt="qr-code" height="100">
                                                        <img src="${qrCode2Url}" alt="qr-code" height="100">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row mb-5">
                                                <div class="col-4">
                                                    <h5 class="info-label">Invoice Date:</h5>
                                                    <p class="info-text">${issueDate}</p>
                                                </div>
                                                <div class="col-4">
                                                    <h5 class="info-label">Due Date:</h5>
                                                    <p class="info-text">${issueDate}</p>
                                                </div>
                                            </div>

                                            <div class="table-responsive">
                                                <table class="table invoice-items-table text-center table-nowrap align-middle mb-0">
                                                    <thead>
                                                        <tr class="bg-light bg-opacity-50">
                                                            <th style="width: 50px;">#</th>
                                                            <th class="text-start">Product Details</th>
                                                            <th>Quantity</th>
                                                            <th>Unit price</th>
                                                            <th class="text-end">Amount</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <th scope="row">01</th>
                                                            <td class="text-start">
                                                                <span class="fw-medium">Room Rent</span>
                                                                <p class="text-muted mb-0">(Monthly rental fee)</p>
                                                            </td>
                                                            <td>1</td>
                                                            <td>${currencyFormat(roomPrice)}</td>
                                                            <td class="text-end">${currencyFormat(roomPrice)}</td>
                                                        </tr>

                                                        <tr class="totals-row">
                                                            <td colspan="3"></td> <td class="text-end">Subtotal</td>
                                                            <td class="text-end">${currencyFormat(subtotal)}</td>
                                                        </tr>
                                                        <tr class="final-total-row">
                                                            <td colspan="3"></td> <td class="text-end">Total Amount</td>
                                                            <td class="text-end">${currencyFormat(totalAmount)}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </body>
                                </html>
                            `;

                    const iframe = document.createElement('iframe');
                    iframe.style.display = 'none';
                    document.body.appendChild(iframe);

                    iframe.onload = function () {
                        iframe.contentWindow.print();
                        document.body.removeChild(iframe);
                    };

                    iframe.contentDocument.write(invoiceHtml);
                    iframe.contentDocument.close();
                });
            }
        });
    </script>
@endpush