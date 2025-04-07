<?php

namespace App\Http\Controllers\Tenant;

use App\DTOs\GiftCardDTO;
use App\DTOs\GiftCardUpdateDTO;
use App\DTOs\RechargeGiftCardDTO;
use App\Http\Controllers\Controller;
use App\Services\Tenant\GiftCardService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GiftCardController extends Controller
{
    protected GiftCardService $giftCardService;


    public function __construct(GiftCardService $giftCardService)
    {
        $this->giftCardService = $giftCardService;
    }

    /**
     * Display the gift card index page.
     *
     * This method ensures the user has the necessary permissions before retrieving
     * all gift card data via the service layer and returning the index view.
     *
     * @return View|RedirectResponse The gift card index view.
     * @throws Exception If an error occurs during retrieval.
     */
    public function index(): View|RedirectResponse
    {
        try {
            // Check if the user is authorized to view gift cards
            $this->authorize('unit');

            // Retrieve all gift card data from the service layer
            $data = $this->giftCardService->getIndexData();

            // Return the index view with the retrieved data
            return view('Tenant.gift_card.index', $data);
        } catch (Exception $e) {
            // Redirect back with an error message if an exception occurs
            return redirect()->back()->with(['not_permitted' => __($e->getMessage())]);
        }
    }

    /**
     * Generate a unique gift card code.
     *
     * This method utilizes the service layer to generate a unique gift card code
     * and returns the result as a JSON response.
     *
     * @return JsonResponse JSON response containing the generated code.
     * @throws Exception If code generation fails.
     */
    public function generateCode(): JsonResponse
    {
        try {
            // Generate a unique gift card code
            $id = $this->giftCardService->generateCode();

            // Return the generated code in a JSON response
            return response()->json($id);
        } catch (Exception $e) {
            // Handle error gracefully
            return response()->json("Error generating gift card code");
        }
    }

    /**
     * Store a newly created gift card.
     *
     * This method validates incoming request data, converts it into a DTO,
     * and passes it to the service layer for gift card creation.
     *
     * @param Request $request The request containing gift card details.
     * @return RedirectResponse Redirect response indicating success or failure.
     * @throws Exception If gift card creation fails.
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            // Convert request data into a DTO
            $dto = GiftCardDTO::fromRequest($request);

            // Create the gift card using the service layer
            $this->giftCardService->createGiftCard($dto);

            // Redirect to the index page with a success message
            return redirect()->route('gift_cards.index')->with('message', 'Gift card created successfully');
        } catch (Exception $e) {
            // Redirect back with an error message if an exception occurs
            return redirect()->back()->with(['not_permitted' => __($e->getMessage())]);
        }
    }

    /**
     * Retrieve gift card details for editing.
     *
     * This method fetches the gift card data from the service layer and returns it
     * as a JSON response. If the gift card is not found, an error message is returned.
     *
     * @param int $id The ID of the gift card.
     * @return JsonResponse JSON response containing gift card data or an error message.
     * @throws Exception If retrieval fails.
     */
    public function edit(int $id): JsonResponse
    {
        try {
            // Fetch and return the gift card data
            return response()->json($this->giftCardService->edit($id));
        } catch (ModelNotFoundException $exception) {
            // Handle case where gift card is not found
            return response()->json('Failed to get GiftCard data!');
        } catch (Exception $e) {
            // Handle general exceptions
            return response()->json('Failed to get GiftCard data!');
        }
    }

    /**
     * Update an existing gift card.
     *
     * This method validates incoming request data, converts it into a DTO,
     * and passes it to the service layer for updating the gift card details.
     *
     * @param Request $request The request containing updated gift card details.
     * @param int $id The ID of the gift card to be updated.
     * @return RedirectResponse Redirect response indicating success or failure.
     * @throws Exception If the gift card update fails.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        try {
            // Convert request data into a DTO
            $dto = GiftCardUpdateDTO::fromRequest($request);

            // Update the gift card using the service layer
            $this->giftCardService->updateGiftCard($dto);

            // Redirect to the gift card list with a success message
            return redirect('gift_cards')->with('message', 'GiftCard updated successfully');
        } catch (ModelNotFoundException $exception) {
            // Handle case where the gift card is not found
            return redirect()->back()->with('not_permitted', $exception->getMessage());
        } catch (Exception $exception) {
            // Handle general exceptions during update
            return redirect()->back()->with('not_permitted', $exception->getMessage());
        }
    }

    /**
     * Recharge a gift card with a specified amount.
     *
     * This method retrieves the gift card, updates its balance, records the transaction,
     * and optionally sends an email notification.
     *
     * @param Request $request The request containing recharge details.
     * @return RedirectResponse Redirect response indicating success or failure.
     * @throws Exception If the recharge process fails.
     */
    public function recharge(Request $request): RedirectResponse
    {
        // Convert request data into a DTO
        $dto = RechargeGiftCardDTO::fromRequest($request);

        try {
            // Handle the recharge process via the service layer
            $message = $this->giftCardService->handle($dto);

            // Redirect to the gift card list with a success message
            return redirect('gift_cards')->with('message', $message);
        } catch (Exception $e) {
            // Redirect back with a failure message
            return redirect()->back()->with('not_permitted', 'Something went wrong while recharging the gift card.');
        }
    }

    /**
     * Delete multiple gift cards selected by the user.
     *
     * This method passes the selected gift card IDs to the service layer for batch deletion.
     *
     * @param Request $request The request containing selected gift card IDs.
     * @return JsonResponse JSON response indicating success or failure.
     * @throws Exception If deletion fails.
     */
    public function deleteBySelection(Request $request): JsonResponse
    {
        try {
            // Delete the selected gift cards using the service layer
            $this->giftCardService->deleteGiftCard($request->input('gift_cardIdArray'));

            // Return a success response
            return response()->json('Gift Card deleted successfully!');
        } catch (ModelNotFoundException $exception) {
            // Handle case where a gift card is not found
            return response()->json($exception->getMessage());
        } catch (Exception $e) {
            // Handle general exceptions during deletion
            return response()->json($e->getMessage());
        }
    }

    /**
     * Delete a specific gift card by its ID.
     *
     * This method calls the service layer to delete the gift card and redirects back
     * with a success or failure message.
     *
     * @param int $id The ID of the gift card to be deleted.
     * @return RedirectResponse Redirect response indicating success or failure.
     * @throws Exception If deletion fails.
     */
    public function destroy(int $id): RedirectResponse
    {
        try {
            // Call the service layer to delete the specified gift card
            $this->giftCardService->destroy($id);

            // Redirect back with a success message
            return redirect()->back()->with('message', 'Gift Card deleted successfully');
        } catch (ModelNotFoundException $exception) {
            // Handle case where the gift card is not found
            return redirect()->back()->with(['not_permitted' => $exception->getMessage()]);
        } catch (Exception $e) {
            // Handle general exceptions during deletion
            return redirect()->back()->with(['not_permitted' => $e->getMessage()]);
        }
    }
}
