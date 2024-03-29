defmodule SpacedRep.Cards do
  @moduledoc """
  The Cards context.
  """

  import Ecto.Query, warn: false
  alias SpacedRep.Repo

  alias SpacedRep.Cards.Card

  @doc """
  Returns the list of cards.

  ## Examples

      iex> list_cards(deck_id)
      [%Card{}, ...]

  """
  def list_cards(deck_id) do
    query = from(c in Card, where: c.deck_id == ^deck_id)
    Repo.all(query) |> Repo.preload(:answers)
  end

  @doc """
  Gets a single card.

  Returns `nil` if the Card does not exist.

  ## Examples

      iex> get_card!(123)
      %Card{}

      iex> get_card(456)
      ** nil

  """
  def get_card(id) do
    query = from c in Card, where: is_nil(c.deleted_at) and c.id == ^id

    Repo.one(query) |> Repo.preload(:answers)
  end

  @doc """
  Creates a card.

  ## Examples

      iex> create_card(%{field: value})
      {:ok, %Card{}}

      iex> create_card(%{field: bad_value})
      {:error, %Ecto.Changeset{}}

  """
  def create_card(deck_id, attrs \\ %{}) do
    %Card{}
    |> Card.changeset(deck_id, attrs)
    |> Repo.insert()
  end

  defp get_updated_card(card, %{"quality" => quality} = attrs) do
    dq = 5 - quality
    updated_easiness = max(1.3, card.easiness + (0.1 - dq * (0.08 + dq * 0.02)))
    updated_easiness = Float.round(updated_easiness, 1)

    updated_repetitions = if quality < 3, do: 0, else: card.repetitions + 1

    updated_interval =
      if quality < 3 do
        1
      else
        case card.repetitions do
          0 -> 1
          1 -> 6
          _ -> trunc(card.interval * updated_easiness)
        end
      end

    next_practice_date =
      DateTime.utc_now()
      |> DateTime.add(updated_interval, :day)

    Map.merge(
      attrs,
      %{
        "easiness" => updated_easiness,
        "repetitions" => updated_repetitions,
        "interval" => updated_interval,
        "next_practice_date" => next_practice_date
      }
    )
  end

  def update_card(%Card{} = card, %{"quality" => quality} = attrs)
      when quality !== card.quality do
    updated_attrs = get_updated_card(card, attrs)

    card
    |> Card.changeset(updated_attrs)
    |> Repo.update()
  end

  @doc """
  Updates a card.

  ## Examples

      iex> update_card(card, %{field: new_value})
      {:ok, %Card{}}

      iex> update_card(card, %{field: bad_value})
      {:error, %Ecto.Changeset{}}

  """

  def update_card(%Card{} = card, attrs) do
    card
    |> Card.changeset(attrs)
    |> Repo.update()
  end

  @doc """
  Deletes a card.

  ## Examples

      iex> delete_card(card)
      {:ok, %Card{}}

      iex> delete_card(card)
      {:error, %Ecto.Changeset{}}

  """
  def delete_card(id) do
    %Card{id: id}
    |> change_card(%{deleted_at: DateTime.utc_now()})
    |> Repo.update()
  end

  @doc """
  Returns an `%Ecto.Changeset{}` for tracking card changes.

  ## Examples

      iex> change_card(card)
      %Ecto.Changeset{data: %Card{}}

  """
  def change_card(%Card{} = card, attrs \\ %{}) do
    Card.changeset(card, attrs)
  end
end
