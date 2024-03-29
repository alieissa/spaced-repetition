defmodule SpacedRepWeb.DeckJSON do
  alias SpacedRep.Decks.Deck

  @doc """
  Renders a list of decks.
  """
  def index(%{decks: decks}) do
    for(deck <- decks, do: data(deck))
  end

  @doc """
  Renders a single deck.
  """
  def show(%{deck: deck}) do
    full_data(deck)
  end

  defp data(%Deck{} = deck) do
    %{
      id: deck.id,
      description: deck.description,
      name: deck.name
    }
  end

  defp full_data(%Deck{} = deck) do
    %{
      id: deck.id,
      description: deck.description,
      name: deck.name,
      cards: deck.cards
    }
  end
end
