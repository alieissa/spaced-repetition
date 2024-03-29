defmodule SpacedRepWeb.AnswerControllerTest do
  use SpacedRepWeb.ConnCase

  import SpacedRep.Factory
  alias SpacedRep.Answers.Answer
  alias Ecto.UUID
  alias SpacedRep.TestUtils, as: Utils

  @create_attrs %{
    content: "some content"
  }
  @update_attrs %{
    content: "some updated content"
  }
  @invalid_attrs %{content: nil}

  setup %{conn: conn} do
    token = Utils.get_token(%{"sub" => UUID.autogenerate()})

    conn =
      conn
      |> put_req_header("authorization", "bearer #{token}")
      |> put_req_header("accept", "application/json")

    {:ok, conn: conn}
  end

  describe "index" do
    setup [:create_answer]

    test "lists all answers", %{conn: conn, answer: %Answer{id: id, content: content, card: card}} do
      conn = get(conn, ~p"/decks/#{card.deck_id}/cards/#{card.id}/answers")

      assert json_response(conn, 200) == [
               %{"id" => id, "content" => content}
             ]
    end
  end

  describe "create answer" do
    setup [:create_answer]

    test "renders answer when data is valid", %{conn: conn, answer: %Answer{card: card}} do
      conn = post(conn, ~p"/decks/#{card.deck_id}/cards/#{card.id}/answers", @create_attrs)

      assert %{"id" => id} = json_response(conn, 201)

      conn = get(conn, ~p"/decks/#{card.deck_id}/cards/#{card.id}/answers/#{id}")

      assert %{
               "id" => ^id,
               "content" => "some content"
             } = json_response(conn, 200)
    end

    test "renders errors when data is invalid", %{conn: conn, answer: %Answer{card: card}} do
      conn = post(conn, ~p"/decks/#{card.deck_id}/cards/#{card.id}/answers", @invalid_attrs)

      assert json_response(conn, 422)["errors"] != %{}
    end
  end

  describe "update answer" do
    setup [:create_answer]

    test "renders answer when data is valid", %{conn: conn, answer: %Answer{id: id, card: card}} do
      conn = put(conn, ~p"/decks/#{card.deck_id}/cards/#{card.id}/answers/#{id}", @update_attrs)

      assert %{"id" => ^id} = json_response(conn, 200)

      conn = get(conn, ~p"/decks/#{card.deck_id}/cards/#{card.id}/answers/#{id}")

      assert %{
               "id" => ^id,
               "content" => "some updated content"
             } = json_response(conn, 200)
    end

    test "renders errors when data is invalid", %{conn: conn, answer: %Answer{id: id, card: card}} do
      conn = put(conn, ~p"/decks/#{card.deck_id}/cards/#{card.id}/answers/#{id}", @invalid_attrs)

      assert json_response(conn, 422)["errors"] != %{}
    end
  end

  describe "delete answer" do
    setup [:create_answer]

    test "deletes chosen answer", %{conn: conn, answer: %Answer{id: id, card: card}} do
      conn = delete(conn, ~p"/decks/#{card.deck_id}/cards/#{card.id}/answers/#{id}")
      assert response(conn, 204)

      assert_error_sent 404, fn ->
        get(conn, ~p"/decks/#{card.deck_id}/cards/#{card.id}/answers/#{id}")
      end
    end
  end

  describe "check answer" do
    test "checks answer again other answers in card", %{conn: conn} do
      card = insert(:card, %{answers: [%{content: "test 123"}]})

      conn =
        post(conn, ~p"/decks/#{card.deck_id}/cards/#{card.id}/answers/check", %{
          "answer" => "test"
        })

      assert json_response(conn, 200)["answer"]["content"] == "test 123"
      assert json_response(conn, 200)["distance"] == 0.5
    end
  end

  defp create_answer(_) do
    answer = insert(:answer)
    %{answer: answer}
  end
end
