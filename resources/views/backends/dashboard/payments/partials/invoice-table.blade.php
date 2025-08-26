<div class="mt-4">
    <div class="table-responsive">
        <table class="table text-center table-nowrap align-middle mb-0">
            <thead class="bg-light bg-opacity-50">
                <tr>
                    <th class="border-0" style="width: 5%;">#</th>
                    <th class="border-0 text-start">Details</th>
                    <th class="border-0" style="width: 15%;">Quantity</th>
                    <th class="border-0" style="width: 20%;">Unit Price</th>
                    <th class="border-0 text-end" style="width: 20%;">Amount</th>
                </tr>
            </thead>
            {{-- This tbody is now initially empty. JS will build all rows here. --}}
            <tbody class="invoice-items-body" data-type="{{ $type }}">
                {{-- A message will show here until a contract is selected --}}
                <tr>
                    <td colspan="5" class="text-center text-muted p-4">
                        Please select a contract to load invoice items.
                    </td>
                </tr>
            </tbody>
        </table>
        {{-- A small loader icon that shows during the AJAX call --}}
        <div class="utility-loader p-3 text-center" style="display: none;">
             <div class="spinner-border spinner-border-sm" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>
</div>