<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ManagerController;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BranchsController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ContractsController;
use App\Http\Controllers\ContractRequestController;
use App\Http\Controllers\EquipmentTypeController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentsController;
use App\Http\Controllers\PesticideAnalyticsController;
use App\Http\Controllers\PesticideController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\sales;
use App\Http\Controllers\SalesManagerController;
use App\Http\Controllers\shared;
use App\Http\Controllers\TargetInsectController;
use App\Http\Controllers\TargetInsectAnalyticsController;
use App\Http\Controllers\TeamLeaderController;
use App\Http\Controllers\TechnicalController;
use App\Http\Controllers\TiketsController;
use App\Http\Controllers\ValidationController;
use App\Http\Controllers\TeamKpiController;
use Illuminate\Support\Facades\Route;

// Language Switching Route
Route::get('/language/{lang}', [LanguageController::class, 'switchLang'])->name('switch.language');

Route::get('/', function () {
    return redirect()->route('login');
});

// Login Routes
Route::controller(LoginController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login');
    Route::post('/login', 'login')->name('login.submit');
    Route::post('/logout', 'logout')->name('logout');
})->middleware('throttle:5,1', 'auth:web,client', 'prevent-back-history');

Route::middleware(['auth:web,client', 'prevent-back-history'])->group(function () {
    Route::get('/change-user-profile', [shared::class, 'changeUserProfile'])->name('change.user.profile');
    Route::post('/update-user-profile', [shared::class, 'updateUserProfile'])->name('update.user.profile');
    Route::get('/change-user-password', [shared::class, 'changeUserpassword'])->name('change.user.password');
    Route::post('/update-user-password', [shared::class, 'updateUserpassword'])->name('update.user.password');
});

// Notification Routes
Route::middleware(['prevent-back-history', 'auth:web,client'])->group(function () {
    Route::get('/notifications', [NotificationController::class, 'getNotifications'])->name('notifications.get');
    Route::post('/notifications/mark-as-read/{id}', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    // Payment QR View Route - Publicly accessible
    Route::get('/payment/qr/{id}', [PaymentsController::class, 'qrView'])->name('payment.qr.view');
    // Payment Details Route
    Route::get('/Payment/view Payment Details/{id}', [PaymentsController::class, 'show'])->name('payment.show');
    Route::get('/payments/{id}/pdf', [PaymentsController::class, 'generatePDF'])->name('payments.pdf');
    Route::get('/contract/{id}/pdf', [ContractsController::class, 'generatePDF'])->name('contract.pdf.generate');
    // Report PDF Generation Route
    Route::post('/reports/pdf', [ReportsController::class, 'generatePDF'])->name('reports.pdf');

    // Shared Contract Insect Analytics - accessible by technical, sales, and clients
    Route::get('/contract/{contract}/insect-analytics', [TargetInsectAnalyticsController::class, 'contractAnalytics'])
        ->middleware(['auth'])
        ->name('contract.insect-analytics');
        
    // Branch-specific Insect Analytics
    Route::get('/contract/{contractId}/branches', [TargetInsectAnalyticsController::class, 'contractBranchSelection'])
        ->middleware(['auth'])
        ->name('analytics.contract.branches');
        
    Route::get('/contract/{contractId}/branch/{branchId}/analytics', [TargetInsectAnalyticsController::class, 'branchAnalytics'])
        ->middleware(['auth'])
        ->name('analytics.branch');

    // sales.show.ticket
    Route::get('/sales/tikets', [TiketsController::class, 'index'])->name('sales.show.ticket');

    // Contract Annex Routes
    Route::get('/contracts/{contract}/annex/create', [ContractsController::class, 'createAnnex'])->name('contracts.annex.create');
    Route::post('/contracts/{contract}/annex', [ContractsController::class, 'storeAnnex'])->name('contracts.annex.store');
    Route::get('/contracts/{contract}/annex/{annex}/edit', [ContractsController::class, 'editAnnex'])->name('contracts.annex.edit');
    Route::put('/contracts/{contract}/annex/{annex}', [ContractsController::class, 'updateAnnex'])->name('contracts.annex.update');
    Route::delete('/contracts/{contract}/annex/{annex}', [ContractsController::class, 'destroyAnnex'])->name('contracts.annex.destroy');
    Route::get('/contracts/annex/{id}/details', [ContractsController::class, 'getAnnexDetails'])->name('contracts.annex.details');

    // Branch Management Routes
    Route::get('/api/branches/{id}', [BranchsController::class, 'index'])->name('branches.get');
    Route::put('/branches/{id}', [BranchsController::class, 'update'])->name('branches.update');

    Route::get('/reports/sales/pdf', [sales::class, 'generatePDF'])->name('sales.report.pdf');
});

// Sales Routes
Route::middleware(['auth', 'role:sales', 'prevent-back-history'])->group(function () {
    Route::get('/sales/dashboard', [sales::class, 'index'])->name('sales.dashboard');
    Route::get('/sales/generate-report', [sales::class, 'generateReport'])->name('sales.generate-report');
    Route::get('/sales/Create-New-Contract', [sales::class, 'contractTypeCards'])->name('sales.contract.type.cards');
    Route::get('/sales/My Client Details/{id}', [sales::class, 'showClientDetails'])->name('view.my.clients.details');
    Route::patch('/sales/update-client/{id}', [sales::class, 'updateClientInfo'])->name('sales.update.client.info');
    Route::get('/sales/My Clients', [sales::class, 'view_my_clients'])->name('view.my.clients');
    Route::get('/sales/To-Do', [sales::class, 'toDoList'])->name('sales.todo');
    Route::post('/sales/Create-New-Contract', [ContractsController::class, 'index'])->name('contract.index');
    Route::post('/sales/Create-Contract', [ContractsController::class, 'create'])->name('contract.create');
    Route::get('/equipment-contract/create', [ContractsController::class, 'createEquipmentContract'])->name('equipment.contract.create');
    Route::post('/equipment-contract/store', [ContractsController::class, 'storeEquipmentContract'])->name('equipment.contract.store');
    Route::get('/sales/Show Active contracts', [ContractsController::class, 'show'])->name('contract.show');
    Route::get('/sales/Show contracts Details/{id}', [ContractsController::class, 'showcontractDetails'])->name('contract.show.details');
    Route::get('/sales/Show Contract Visit Details/{id}', [ContractsController::class, 'showContractVisitDetails'])->name('view.contract.visit');
    Route::get('/sales/visit/{visit}/report', [ContractsController::class, 'viewVisitReport'])->name('contract.visit.report');
    Route::post('/contract-requests/{id}/{action}', [ContractRequestController::class, 'handleRequest'])->where('action', 'approve|reject');
    Route::get('/sales/edit-Contract/{id}', [ContractsController::class, 'edit'])->name('contract.edit');
    Route::patch('/sales/update-Contract/{id}', [ContractsController::class, 'update'])->name('contract.update');
    Route::patch('/sales/stop-contract/{id}', [ContractsController::class, 'stop_contract'])->name('contract.stop');
    Route::delete('/sales/delete-contract/{id}', [ContractsController::class, 'destroy'])->name('contract.delete');
    Route::get('/sales/Show All Completed Contract', [ContractsController::class, 'view_completed_contracts'])->name('completed.show.all');
    Route::get('/sales/Show All Stopped Contract', [ContractsController::class, 'view_stopped_contract'])->name('stopped.show.all');
    Route::get('/sales/Show All canceled Contract', [ContractsController::class, 'view_cancelled_contracts'])->name('canceled.show.all');
    Route::patch('/sales/return-contract/{id}', [sales::class, 'return_contract'])->name('contract.return');
    Route::get('/sales/show Tikets', [TiketsController::class, 'index'])->name('sales.tikets');
    
    // Contract Renewal Routes
    Route::get('/contracts/{id}/renewal', [ContractsController::class, 'showRenewalForm'])->name('contract.renewal.form');
    Route::post('/contracts/{id}/renewal', [ContractsController::class, 'processRenewal'])->name('contract.renewal.process');
    Route::post('/contracts/{id}/renewal-response', [ContractsController::class, 'handleRenewalResponse'])->name('contract.renewal.response');
    
    // Postponement Request Routes
    Route::get('/postponement-requests', [ContractsController::class, 'postponement_requests'])
        ->name('postponement.requests');
    Route::patch('/Payments/{id}/mark-as-paid', [PaymentsController::class, 'markAsPaid'])->name('payments.markAsPaid');
    Route::get('/Payments/{id}/details', [PaymentsController::class, 'getDetails'])->name('payments.details');

    // sales.show.ticket
    Route::get('/sales/tikets', [TiketsController::class, 'index'])->name('sales.show.ticket');

    // Contract Annex Routes
    Route::get('/contracts/{contract}/annex/create', [ContractsController::class, 'createAnnex'])->name('contracts.annex.create');
    Route::post('/contracts/{contract}/annex', [ContractsController::class, 'storeAnnex'])->name('contracts.annex.store');
    Route::get('/contracts/{contract}/annex/{annex}/edit', [ContractsController::class, 'editAnnex'])->name('contracts.annex.edit');
    Route::put('/contracts/{contract}/annex/{annex}', [ContractsController::class, 'updateAnnex'])->name('contracts.annex.update');
    Route::delete('/contracts/{contract}/annex/{annex}', [ContractsController::class, 'destroyAnnex'])->name('contracts.annex.destroy');
    Route::get('/contracts/annex/{id}/details', [ContractsController::class, 'getAnnexDetails'])->name('contracts.annex.details');

    // Branch Management Routes
    Route::get('/api/branches/{id}', [BranchsController::class, 'index'])->name('branches.get');
    Route::put('/branches/{id}', [BranchsController::class, 'update'])->name('branches.update');

    Route::get('/reports/sales/pdf', [sales::class, 'generatePDF'])->name('sales.report.pdf');
});

// Sales Manager Routes
Route::middleware(['auth', 'role:sales_manager', 'prevent-back-history'])->group(function () {
    Route::get('/sales-manager/dashboard', [SalesManagerController::class, 'index'])->name('sales_manager.dashboard');
    Route::get('/sales-manager/agents', [SalesManagerController::class, 'manageAgents'])->name('sales_manager.manage_agents');
    Route::post('/sales-manager/agents', [SalesManagerController::class, 'storeAgent'])->name('sales_manager.store_agent');
    Route::put('/sales-manager/agents/{id}', [SalesManagerController::class, 'updateAgent'])->name('sales_manager.update_agent');
    Route::delete('/sales-manager/agents/{id}', [SalesManagerController::class, 'removeAgent'])->name('sales_manager.remove_agent');

    Route::get('/sales-manager/show-agents', [SalesManagerController::class, 'show'])->name('sales_manager.show');
    Route::get('/sales-manager/show-agent-clients/{id}', [SalesManagerController::class, 'showSalesAgentClients'])->name('sales_manager.show.clients');
    Route::get('/sales-manager/agent/{id}/contracts', [SalesManagerController::class, 'agentContracts'])
        ->name('sales_manager.agent.contracts');
    Route::get('/sales-manager/agent/{id}/performance', [SalesManagerController::class, 'agentPerformance'])
        ->name('sales_manager.agent.performance');
    // Reports Routes
    Route::get('/sales-manager/reports/contacts', [SalesManagerController::class, 'contactsReport'])->name('sales_manager.reports.contacts');
    Route::get('/sales-manager/reports/contracts', [SalesManagerController::class, 'contractsReport'])->name('sales_manager.reports.contracts');
    Route::get('/sales-manager/reports/collections', [SalesManagerController::class, 'collectionsReport'])->name('sales_manager.reports.collections');
    Route::get('/sales-manager/reports/payments', [SalesManagerController::class, 'paymentsReport'])->name('sales_manager.reports.payments');
    Route::get('/sales-manager/reports/invoices', [SalesManagerController::class, 'invoicesReport'])->name('sales_manager.reports.invoices');

    // Contract Management Routes
    Route::get('/sales-manager/contracts', [SalesManagerController::class, 'manageContracts'])->name('sales_manager.manage_contracts');
    Route::delete('/sales-manager/contracts/{id}', [SalesManagerController::class, 'deleteContract'])->name('sales_manager.delete_contract');
    Route::put('/sales-manager/contracts/{id}/transfer', [SalesManagerController::class, 'transferContract'])->name('sales_manager.transfer_contract');
    Route::get('/sales-manager/contract/{id}', [SalesManagerController::class, 'viewContract'])
        ->name('sales_manager.contract.view');

    // Agent Performance Routes
    Route::get('/sales-manager/performance', [SalesManagerController::class, 'agentPerformance'])->name('sales_manager.performance');
    Route::get('/sales-manager/performance/{id}', [SalesManagerController::class, 'agentPerformance'])->name('sales_manager.agent_performance');

    // Postponement Request Routes
    Route::get('/sales-manager/postponement-requests', [SalesManagerController::class, 'postponementRequests'])
        ->name('sales_manager.postponement_requests');
    Route::post('/sales-manager/postponement/approve', [SalesManagerController::class, 'approvePostponement'])
        ->name('postponement.approve');
    Route::post('/sales-manager/postponement/reject', [SalesManagerController::class, 'rejectPostponement'])
        ->name('postponement.reject');

    // Client Management Routes
    Route::get('/sales-manager/manage-clients', [SalesManagerController::class, 'manageClients'])
        ->name('sales_manager.manage_clients');
    Route::get('/sales-manager/client/{id}', [SalesManagerController::class, 'clientDetails'])
        ->name('sales_manager.client.details');
    Route::get('/sales-manager/clients/{id}/edit', [SalesManagerController::class, 'editClient'])->name('sales.clients.edit');
    Route::put('/sales-manager/clients/{id}', [SalesManagerController::class, 'updateClient'])->name('sales.clients.update');

    // Contract Annex Routes

    Route::post('/contracts/annex/{annex}/approve', [ContractsController::class, 'approveAnnex'])->name('contracts.annex.approve');
    Route::post('/contracts/annex/{annex}/reject', [ContractsController::class, 'rejectAnnex'])->name('contracts.annex.reject');
    Route::get('/sales-manager/pending-annexes', [SalesManagerController::class, 'pendingAnnexes'])
        ->name('sales_manager.pending_annexes');

    // Equipment Types Management
    Route::resource('equipment-types', EquipmentTypeController::class);
    Route::post('equipment-types/{id}/restore', [EquipmentTypeController::class, 'restore'])->name('equipment-types.restore');
});

// Finance Routes
Route::middleware(['auth', 'role:finance', 'prevent-back-history'])->group(function () {
    Route::get('/finance/dashboard', [FinanceController::class, 'dashboard'])->name('finance.dashboard');
    Route::get('/finance/payments', [FinanceController::class, 'payments'])->name('finance.payments');
    Route::get('/finance/payments/pending', [FinanceController::class, 'pendingPayments'])->name('finance.payments.pending');
    Route::get('/finance/payments/{id}', [FinanceController::class, 'showPayment'])->name('finance.payments.show');
    Route::get('/finance/invoices', [FinanceController::class, 'invoices'])->name('finance.invoices');
    Route::get('/finance/invoices/{id}', [FinanceController::class, 'showInvoice'])->name('finance.invoices.show');
    Route::get('/finance/reports/financial', [FinanceController::class, 'generateFinancialReport'])->name('finance.reports.financial');
    Route::patch('/finance/payments/{id}/status', [FinanceController::class, 'updatePaymentStatus'])->name('finance.payments.update-status');
    
    // New routes for enhanced functionality
    Route::get('/finance/payments/{id}/record', [FinanceController::class, 'paymentForm'])->name('finance.payments.form');
    Route::post('/finance/payments/{id}/record', [FinanceController::class, 'recordPayment'])->name('finance.payments.record');
    Route::get('/finance/reports/analytics', [FinanceController::class, 'advancedAnalytics'])->name('finance.reports.analytics');
    Route::get('/finance/exports/payments/form', [FinanceController::class, 'exportPaymentsForm'])->name('finance.exports.payments');
    Route::get('/finance/exports/payments/download', [FinanceController::class, 'exportPayments'])->name('finance.exports.payments.download');
    Route::post('/finance/notifications/send-reminders', [FinanceController::class, 'sendPaymentReminders'])->name('finance.notifications.send-reminders');
    Route::get('/finance/reconciliation', [FinanceController::class, 'reconciliationIndex'])->name('finance.reconciliation.index');
    Route::post('/finance/reconciliation/mark', [FinanceController::class, 'reconcilePayments'])->name('finance.reconciliation.mark');
});

// Technical Routes
Route::middleware(['auth', 'role:technical'])->group(function () {
    Route::get('/technical/dashboard', [TechnicalController::class, 'dashboard'])->name('technical.dashboard');
    // Team management Routes
    Route::get('/teams', [TechnicalController::class, 'index'])->name('teams.index');
    Route::post('/teams', [TechnicalController::class, 'create'])->name('teams.create');
    
    // Team KPI Routes
    Route::get('/technical/team-kpi', [TeamKpiController::class, 'index'])->name('technical.team.kpi');
    Route::get('/technical/team-kpi/compare', [TeamKpiController::class, 'compareTeams'])->name('technical.team.kpi.compare');
    Route::get('/technical/team-kpi/{id}', [TeamKpiController::class, 'teamDetail'])->name('technical.team.kpi.detail');
    Route::post('/technical/team-kpi/pdf', [TeamKpiController::class, 'generatePdfReport'])->name('technical.team.kpi.pdf');
    
    Route::put('/teams/{id}', [TechnicalController::class, 'modify'])->name('teams.modify');
    Route::delete('/teams/{id}', [TechnicalController::class, 'delete'])->name('teams.delete');
    // Pesticide Management Routes
    Route::prefix('technical/pesticides')->name('technical.pesticides.')->group(function () {
        Route::get('/', [PesticideController::class, 'index'])->name('index');
        Route::get('/create', [PesticideController::class, 'create'])->name('create');
        Route::post('/store', [PesticideController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [PesticideController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [PesticideController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [PesticideController::class, 'destroy'])->name('destroy');
        Route::get('/export', [PesticideController::class, 'export'])->name('export');
    });

    // Target Insects Management
    Route::prefix('technical/target-insects')->middleware(['auth', 'role:technical'])->group(function () {
        Route::get('/', [TargetInsectController::class, 'index'])->name('target-insects.index');
        Route::get('/create', [TargetInsectController::class, 'create'])->name('target-insects.create');
        Route::post('/', [TargetInsectController::class, 'store'])->name('target-insects.store');
        Route::get('/{targetInsect}/edit', [TargetInsectController::class, 'edit'])->name('target-insects.edit');
        Route::put('/{targetInsect}', [TargetInsectController::class, 'update'])->name('target-insects.update');
        Route::delete('/{targetInsect}', [TargetInsectController::class, 'destroy'])->name('target-insects.destroy');
        
        // Target Insects Analytics
        Route::get('/analytics', [TargetInsectAnalyticsController::class, 'index'])->name('target-insects.analytics');
    });

    // Pesticide Analytics Routes
    Route::get('technical/pesticides/analytics', [PesticideAnalyticsController::class, 'index'])->name('technical.pesticides.analytics');
    Route::get('technical/pesticides/analytics/team/{teamId}', [PesticideAnalyticsController::class, 'teamReport'])->name('technical.pesticides.analytics.teamReport');
    Route::get('technical/pesticides/analytics/pesticide/{pesticideSlug}', [PesticideAnalyticsController::class, 'pesticideReport'])->name('technical.pesticides.analytics.pesticideReport');
    
    // Workers and Team Leaders management
    Route::get('/workers', [TechnicalController::class, 'workersIndex'])->name('workers.index');
    Route::post('/workers', [TechnicalController::class, 'createWorker'])->name('workers.create');
    Route::put('/workers/{id}', [TechnicalController::class, 'updateWorker'])->name('workers.update');
    Route::delete('/workers/{id}', [TechnicalController::class, 'deleteWorker'])->name('workers.delete');
    Route::get('/team-leaders', [TechnicalController::class, 'teamLeadersIndex'])->name('team-leaders.index');
    Route::post('/team-leaders', [TechnicalController::class, 'createTeamLeader'])->name('team-leaders.create');
    Route::put('/team-leaders/{id}', [TechnicalController::class, 'updateTeamLeader'])->name('team-leaders.update');
    Route::delete('/team-leaders/{id}', [TechnicalController::class, 'deleteTeamLeader'])->name('team-leaders.delete');

    // Technical Manager Appointment Routes
    Route::get('/technical/appointments', [TechnicalController::class, 'viewScheduledAppointments'])
        ->name('technical.scheduled-appointments');
    Route::get('/technical/appointments/{appointment}/complete', [TechnicalController::class, 'markAppointmentComplete'])
        ->name('technical.appointment.complete');
    Route::put('/technical/appointments/{appointment}/cancel', [TechnicalController::class, 'cancelAppointment'])
        ->name('technical.appointment.cancel');
    Route::put('/technical/appointments/{appointment}/edit', [TechnicalController::class, 'editAppointment'])
        ->name('technical.appointment.edit');

    // Technical Manager Visit Routes
    Route::get('/technical/visits/completed', [TechnicalController::class, 'viewCompletedVisits'])
        ->name('technical.completed-visits');
    Route::get('/technical/visits/cancelled', [TechnicalController::class, 'viewCancelledVisits'])
        ->name('technical.cancelled-visits');
    Route::put('/technical/visits/{visit}/reschedule', [TechnicalController::class, 'rescheduleVisit'])
        ->name('technical.reschedule-visit');

    // Visit Reports
    Route::get('/technical/visit/{id}/report/view', [TechnicalController::class, 'viewReport'])
        ->name('technical.visit.report.view');

    // Visit Scheduling
    Route::post('/technical/schedule-visit', [TechnicalController::class, 'scheduleVisit'])->name('technical.schedule_visit');
    Route::get('/api/clients/{client}/contracts', [TechnicalController::class, 'getClientContracts'])->name('api.client.contracts');
    Route::get('/api/contracts/{contract}/branches', [TechnicalController::class, 'getContractBranches'])->name('api.contract.branches');

    // Contract Details
    Route::get('/technical/contract/{id}', [TechnicalController::class, 'viewContractDetails'])
        ->name('technical.contract.show');

    // Client Tickets Routes
    Route::get('/technical/client-tickets', [TechnicalController::class, 'clientTickets'])->name('technical.client_tickets');
    Route::get('/technical/client-tickets/{id}', [TechnicalController::class, 'showClientTicket'])->name('technical.client_tickets.show');
    Route::post('/technical/client-tickets/{id}/solve', [TechnicalController::class, 'solveClientTicket'])->name('technical.client_tickets.solve');
    Route::post('/technical/client-tickets/{id}/update-status', [TechnicalController::class, 'solveClientTicket'])->name('technical.client_tickets.update_status');
    Route::get('/technical/team-schedules', [TechnicalController::class, 'teamSchedules'])
        ->name('technical.team.schedules');
    Route::get('/technical/visit-change-requests', [TechnicalController::class, 'visitChangeRequests'])
        ->name('technical.visit.requests');
    Route::put('/visit/{id}/update', [ClientController::class, 'updateVisit'])->name('technical.visit.update');
});

// Team Leader Routes
Route::middleware(['auth', 'role:team_leader', 'prevent-back-history'])->group(function () {
    // Dashboard
    Route::get('/team-leader/dashboard', [TeamLeaderController::class, 'dashboard'])
        ->name('team-leader.dashboard');

    // Visits Management
    Route::get('/team-leader/visits', [TeamLeaderController::class, 'visits'])
        ->name('team-leader.visits');
    Route::get('/team-leader/visit/{id}', [TeamLeaderController::class, 'showVisit'])
        ->name('team-leader.visit.show');
    Route::post('/team-leader/visit/{id}/complete', [TeamLeaderController::class, 'completeVisit'])
        ->name('team-leader.visit.complete');

    // Visit Reports
    Route::get('/team-leader/visit/{id}/report/create', [TeamLeaderController::class, 'createReport'])
        ->name('team-leader.visit.report.create');
    Route::post('/team-leader/visit/{id}/report', [TeamLeaderController::class, 'storeReport'])
        ->name('team-leader.visit.report.store');

    // Contract Details
    Route::get('/team-leader/contract/{id}', [TeamLeaderController::class, 'showContract'])
        ->name('team-leader.contract.show');
});

// Client Routes
Route::middleware(['role:client', 'prevent-back-history'])->group(function () {
    // Dashboard & Contract Routes
    Route::get('/client/dashboard', [ClientController::class, 'index'])->name('client.dashboard');
    Route::get('/client/Show Contracts', [ClientController::class, 'show'])->name('client.show');
    Route::get('/client/contract/{id}/details', [ClientController::class, 'contractDetails'])->name('client.contract.details');
    Route::get('/client/Show Contract Details/{id}', [ClientController::class, 'showContractDetails'])->name('client.show.details');
    Route::get('/client/contract/{contract}/visits', [ClientController::class, 'contractVisits'])->name('client.contract.visit.details');
    Route::get('/client/visit/{visit}/details', [ClientController::class, 'visitDetails'])->name('client.visit.details');

    // Contract Actions
    Route::post('/client/contract/{id}/approve', [ClientController::class, 'approveContract'])->name('client.contract.approve');
    Route::post('/client/contract/{id}/reject', [ClientController::class, 'rejectContract'])->name('client.contract.reject');
    Route::post('/client/contract/{id}/update-request', [ClientController::class, 'submitUpdateRequest'])->name('client.contract.update-request');
    Route::get('/client/contract/{id}/download', [ClientController::class, 'downloadContract'])->name('client.contract.download');
    Route::patch('/client/update-contract/{id}', [ClientController::class, 'update'])->name('client.update');
    Route::patch('/client/return-contract/{id}', [ClientController::class, 'return_contract'])->name('client.return');

    // Payment Routes
    Route::get('/client/contract/{id}/payments', [ClientController::class, 'showPaymentDetails'])->name('client.show.payment.details');
    Route::get('/client/payment/{id}/details', [ClientController::class, 'getPaymentDetails'])->name('client.payment.details');
    Route::post('/client/contract/{id}/payment/postpone', [ClientController::class, 'postponePayment'])->name('client.payment.postpone');

    // Tickets Routes
    Route::get('/client/tikets', [TiketsController::class, 'my_tikets'])->name('client.tikets');
    Route::get('/client/tikets/create', [TiketsController::class, 'client_create'])->name('client.tickets.create');
    Route::post('/client/tikets/store', [TiketsController::class, 'client_store'])->name('client.tikets.store');
    Route::get('/client/tikets/{id}', [TiketsController::class, 'show'])->name('client.tikets.show');
    Route::post('/client/tikets/{id}/reply', [TiketsController::class, 'reply'])->name('client.ticket.reply');
    Route::post('/client/tikets/{id}/update-status', [TiketsController::class, 'updateStatus'])->name('client.tikets.update-status');
    Route::post('/client/visit.update', [ClientController::class, 'send_updateVisit'])->name('client.visit.update');
    Route::get('/client/contract/{id}/request-update', [ClientController::class, 'requestContractUpdate'])->name('client.contract.request-update');
    Route::post('/client/contract/{id}/submit-update-request', [ClientController::class, 'submitContractUpdateRequest'])->name('client.contract.submit-update');
    Route::get('/client/contract/{id}/visits', [ClientController::class, 'contractVisits'])->name('client.contract.visits');
    Route::get('/client/test-notification', [ClientController::class, 'testNotification'])->name('client.test-notification');
});

// Alert Routes
Route::middleware(['auth', 'prevent-back-history'])->group(function () {
    Route::get('/alerts', [AlertController::class, 'index'])->name('alerts.index');
    Route::get('/alerts/mark-all-as-read', [AlertController::class, 'markAllAsRead'])->name('alerts.mark-all-as-read');
    Route::get('/alerts/{id}/mark-as-read', [AlertController::class, 'markAsRead'])->name('alerts.mark-as-read');
    Route::delete('/alerts/{alert}', [AlertController::class, 'destroy'])->name('alerts.destroy');
});

Route::get('/check-duplicate', [ValidationController::class, 'checkDuplicate'])->name('check.duplicate');
