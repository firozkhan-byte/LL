<?php

namespace App\Livewire\Admin;

use App\Models\Supplier;
use App\Services\SupplierService;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class SupplierManager extends Component
{
    use WithFileUploads, WithPagination;

    // Filters
    public string $search = '';

    public ?float $selectedRating = null;

    public ?int $selectedCreditDays = null;

    public string $filterStatus = 'active'; // active, pending_approval, inactive, deleted

    // Modal state
    public bool $showingSupplierModal = false;

    public ?string $supplierId = null;

    // Supplier fields
    public string $name = '';

    public ?string $gstin = '';

    public ?string $pan = '';

    public int $paymentTermsDays = 30;

    public float $creditLimit = 0.00;

    public float $rating = 5.00;

    public string $status = 'pending_approval';

    // Nested relations lists
    public array $contactsList = [];

    public array $bankAccountsList = [];

    // File upload holder
    public $uploadedDocuments = [];

    protected $queryString = [
        'search' => ['except' => ''],
        'selectedRating' => ['except' => null],
        'selectedCreditDays' => ['except' => null],
        'filterStatus' => ['except' => 'active'],
    ];

    public function mount(): void
    {
        abort_if(Gate::denies('manage-company'), 403);
    }

    public function render(SupplierService $supplierService)
    {
        $filters = [
            'search' => $this->search,
            'rating' => $this->selectedRating,
            'credit_days' => $this->selectedCreditDays,
            'status' => $this->filterStatus,
        ];

        $suppliers = $supplierService->getSuppliers($filters, 10);
        $metrics = $supplierService->getStats();

        return view('livewire.admin.supplier-manager', [
            'suppliers' => $suppliers,
            'metrics' => $metrics,
        ])->layout('layouts.app');
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showingSupplierModal = true;
    }

    public function openEditModal(string $id, SupplierService $supplierService): void
    {
        $this->resetForm();
        $supplier = $supplierService->getSupplier($id);
        if ($supplier) {
            $this->supplierId = $supplier->id;
            $this->name = $supplier->name;
            $this->gstin = $supplier->gstin;
            $this->pan = $supplier->pan;
            $this->paymentTermsDays = $supplier->payment_terms_days;
            $this->creditLimit = $supplier->credit_limit;
            $this->rating = $supplier->rating;
            $this->status = $supplier->status;

            // Load relations
            foreach ($supplier->contacts as $c) {
                $this->contactsList[] = [
                    'name' => $c->name,
                    'email' => $c->email,
                    'phone' => $c->phone,
                    'designation' => $c->designation,
                    'is_primary' => $c->is_primary,
                ];
            }

            foreach ($supplier->bankAccounts as $b) {
                $this->bankAccountsList[] = [
                    'bank_name' => $b->bank_name,
                    'account_number' => $b->account_number,
                    'ifsc_code' => $b->ifsc_code,
                    'branch_name' => $b->branch_name,
                    'is_primary' => $b->is_primary,
                ];
            }

            $this->showingSupplierModal = true;
        }
    }

    public function addContactLine(): void
    {
        $this->contactsList[] = [
            'name' => '',
            'email' => '',
            'phone' => '',
            'designation' => '',
            'is_primary' => count($this->contactsList) === 0,
        ];
    }

    public function removeContactLine(int $index): void
    {
        unset($this->contactsList[$index]);
        $this->contactsList = array_values($this->contactsList);
    }

    public function addBankLine(): void
    {
        $this->bankAccountsList[] = [
            'bank_name' => '',
            'account_number' => '',
            'ifsc_code' => '',
            'branch_name' => '',
            'is_primary' => count($this->bankAccountsList) === 0,
        ];
    }

    public function removeBankLine(int $index): void
    {
        unset($this->bankAccountsList[$index]);
        $this->bankAccountsList = array_values($this->bankAccountsList);
    }

    public function saveSupplier(SupplierService $supplierService): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'paymentTermsDays' => 'required|integer|min:0',
            'creditLimit' => 'required|numeric|min:0',
            'rating' => 'required|numeric|min:0|max:5',
            'contactsList.*.name' => 'required|string|max:255',
            'contactsList.*.email' => 'required|email',
            'contactsList.*.phone' => 'required|string',
            'bankAccountsList.*.bank_name' => 'required|string',
            'bankAccountsList.*.account_number' => 'required|string',
            'bankAccountsList.*.ifsc_code' => 'required|string',
        ]);

        $payload = [
            'name' => $this->name,
            'gstin' => $this->gstin ?: null,
            'pan' => $this->pan ?: null,
            'payment_terms_days' => $this->paymentTermsDays,
            'credit_limit' => $this->creditLimit,
            'rating' => $this->rating,
            'status' => $this->status,
            'contacts' => $this->contactsList,
            'bank_accounts' => $this->bankAccountsList,
        ];

        // Process uploaded documents if present
        if (! empty($this->uploadedDocuments)) {
            $docs = [];
            foreach ($this->uploadedDocuments as $doc) {
                $path = $doc->store('supplier_docs', 'public');
                $docs[] = [
                    'document_name' => $doc->getClientOriginalName(),
                    'file_path' => $path,
                ];
            }
            $payload['documents'] = $docs;
        }

        if ($this->supplierId) {
            // Updating existing supplier profile triggers proposal or direct update based on status
            if ($this->status === 'pending_approval') {
                session()->flash('message', 'Update proposed for approval.');
                $supplierService->proposeUpdate($this->supplierId, $payload, auth()->id());
            } else {
                $supplierService->updateSupplier($this->supplierId, $payload);
                session()->flash('message', 'Supplier updated successfully.');
            }
        } else {
            // New supplier onboarding goes through proposal
            $supplierService->proposeSupplier($payload, auth()->id());
            session()->flash('message', 'Onboarding proposal created. Waiting for dual authorization.');
        }

        $this->showingSupplierModal = false;
        $this->resetForm();
    }

    public function deleteSupplier(string $id, SupplierService $supplierService): void
    {
        $supplier = $supplierService->getSupplier($id);
        if ($supplier && $supplier->status === 'pending_approval') {
            $supplierService->deleteSupplier($id);
            session()->flash('message', 'Pending supplier deleted.');
        } else {
            $supplierService->proposeDelete($id, auth()->id());
            session()->flash('message', 'Delete proposed for approval.');
        }
    }

    public function restoreSupplier(string $id, SupplierService $supplierService): void
    {
        $supplierService->restoreSupplier($id);
        session()->flash('message', 'Supplier restored successfully.');
    }

    private function resetForm(): void
    {
        $this->supplierId = null;
        $this->name = '';
        $this->gstin = '';
        $this->pan = '';
        $this->paymentTermsDays = 30;
        $this->creditLimit = 0.00;
        $this->rating = 5.00;
        $this->status = 'pending_approval';
        $this->contactsList = [];
        $this->bankAccountsList = [];
        $this->uploadedDocuments = [];
    }
}
